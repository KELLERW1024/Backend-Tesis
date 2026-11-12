<?php

namespace App\Services;

use App\Models\PlanNode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StructureService
{
    public function getByPlan(int $planId)
    {
        return PlanNode::where('plan_id', $planId)
            ->orderBy('orden')
            ->get();
    }

    public function find(int $id)
    {
        return PlanNode::with('children')
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $parentId = $data['parent_id'] ?? null;

            // Si no mandaron orden, agregamos al final
            $orden = $data['orden']
                ?? $this->getNextOrder($parentId, $data['plan_id'], $data['user_plan_id'] ?? null);

            // Desplazar elementos posteriores
            $query = PlanNode::where('plan_id', $data['plan_id'])
                ->where('parent_id', $parentId);
            
            if (isset($data['user_plan_id'])) {
                $query->where('user_plan_id', $data['user_plan_id']);
            } else {
                $query->whereNull('user_plan_id');
            }

            $query->where('orden', '>=', $orden)->increment('orden');

            $nivel = $this->calculateLevel($parentId);

            $newNode = PlanNode::create([
                'user_plan_id' => $data['user_plan_id'] ?? null,
                'plan_id'      => $data['plan_id'],
                'parent_id'    => $parentId,
                'titulo'       => $data['titulo'],
                'orden'        => $orden,
                'objective'    => $data['objective'] ?? null,
                'nivel'        => $nivel,
            ]);

            // RECALCULAR SECUENCIALMENTE CÓDIGOS Y TÍTULOS
            $this->recalculateStructure($data['plan_id'], $data['user_plan_id'] ?? null);

            return $newNode->fresh();
        });
    }

    public function update(int $id, array $data)
    {
        $node = PlanNode::findOrFail($id);

        $node->update([
            'titulo'    => $data['titulo'] ?? $node->titulo,
            'objective' => $data['objective'] ?? $node->objective,
        ]);

        return $node->fresh();
    }

    // MOVER/REORDENAR UN NODO
    public function changePosition(
        int $id,
        ?int $newParentId,
        int $newOrder
    ) {
        return DB::transaction(function () use (
            $id,
            $newParentId,
            $newOrder
        ) {

            $node = PlanNode::lockForUpdate()
                ->findOrFail($id);

            $oldParentId = $node->parent_id;
            $oldOrder = $node->orden;     

            // No permitir que un nodo sea hijo de sí mismo
            if ($newParentId === $node->id) {
                throw new RuntimeException(
                    'Un nodo no puede ser hijo de sí mismo.'
                );
            }

            // Evitar ciclos
            if ($newParentId !== null) {
                $this->validateNoCycle(
                    $node->id,
                    $newParentId
                );
            }

            $query = PlanNode::where('plan_id', $node->plan_id);
            if ($node->user_plan_id !== null) {
                $query->where('user_plan_id', $node->user_plan_id);
            } else {
                $query->whereNull('user_plan_id');
            }

            if ($oldParentId == $newParentId) {
                if ($newOrder < $oldOrder) {
                    (clone $query)->where('parent_id', $oldParentId)
                        ->where('id', '!=', $node->id)
                        ->whereBetween('orden', [$newOrder, $oldOrder - 1])
                        ->increment('orden');
                } elseif ($newOrder > $oldOrder) {
                    (clone $query)->where('parent_id', $oldParentId)
                        ->where('id', '!=', $node->id)
                        ->whereBetween('orden', [$oldOrder + 1, $newOrder])
                        ->decrement('orden');
                }
            } else {
                (clone $query)->where('parent_id', $oldParentId)
                    ->where('orden', '>', $oldOrder)
                    ->decrement('orden');

                (clone $query)->where('parent_id', $newParentId)
                    ->where('orden', '>=', $newOrder)
                    ->increment('orden');
            }

            $node->update([
                'parent_id' => $newParentId,
                'orden'     => $newOrder,
                'nivel'     => $this->calculateLevel($newParentId),
            ]);

            // RECALCULAR SECUENCIALMENTE CÓDIGOS Y TÍTULOS
            $this->recalculateStructure($node->plan_id, $node->user_plan_id);

            return $node->fresh();
        });
    }

    private function validateNoCycle(
        int $nodeId,
        int $newParentId
    ): void {

        $parent = PlanNode::find($newParentId);

        while ($parent) {

            if ($parent->id === $nodeId) {
                throw new RuntimeException(
                    'No puedes mover el nodo dentro de uno de sus descendientes.'
                );
            }

            $parent = $parent->parent;
        }
    }

    // CALCULAR EL NIIVEL +> 
    private function calculateLevel(?int $parentId): int
    {
        if ($parentId === null) {
            return 1;
        }

        $parent = PlanNode::findOrFail($parentId);

        return $parent->nivel + 1;
    }

    // Obtener el siguiente oprden
    private function getNextOrder(
        ?int $parentId,
        int $planId,
        ?int $userPlanId
    ): int {
        $query = PlanNode::where('plan_id', $planId)
            ->where('parent_id', $parentId);

        if ($userPlanId !== null) {
            $query->where('user_plan_id', $userPlanId);
        } else {
            $query->whereNull('user_plan_id');
        }

        return ($query->max('orden') ?? 0) + 1;
    }

    // ELIMINAR 
    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {

            $node = PlanNode::lockForUpdate()
                ->findOrFail($id);

            $planId = $node->plan_id;
            $userPlanId = $node->user_plan_id;
            $parentId = $node->parent_id;
            $orden = $node->orden;

            // Primero eliminar todo el árbol
            $this->deleteChildren($node->id);

            $node->delete();

            // Cerrar hueco
            $query = PlanNode::where('plan_id', $planId)
                ->where('parent_id', $parentId);

            if ($userPlanId !== null) {
                $query->where('user_plan_id', $userPlanId);
            } else {
                $query->whereNull('user_plan_id');
            }

            $query->where('orden', '>', $orden)->decrement('orden');

            // RECALCULAR SECUENCIALMENTE CÓDIGOS Y TÍTULOS TRAS LA ELIMINACIÓN
            $this->recalculateStructure($planId, $userPlanId);
        });
    }

    private function deleteChildren(int $parentId): void
    {
        $children = PlanNode::where('parent_id', $parentId)->get();

        foreach ($children as $child) {
            $this->deleteChildren($child->id);
            $child->delete();
        }
    }

    // =================================================
    // MÉTODO PARA REORDENAR
    // También lo usa ConversationService al crear la estructura del usuario
    // =================================================
    public function recalculateStructure(int $planId, ?int $userPlanId): void
    {
        $romanos = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

        $query = PlanNode::where('plan_id', $planId);
        if ($userPlanId !== null) {
            $query->where('user_plan_id', $userPlanId);
        } else {
            $query->whereNull('user_plan_id');
        }

        // Obtener todos los nodos raíz
        $allRootNodes = (clone $query)->whereNull('parent_id')
            ->orderBy('orden', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Separar y ordenar para respetar el orden:

        $preliminares = $allRootNodes->filter(function($n) {
            $t = mb_strtolower($n->titulo);
            return str_contains($t, 'resumen ejecutivo') || str_contains($t, 'introducción');
        })->sortBy('orden');

        $finales = $allRootNodes->filter(function($n) {
            $t = mb_strtolower($n->titulo);
            return str_contains($t, 'conclusiones') || str_contains($t, 'recomendaciones');
        })->sortBy('orden');

        $capitulosCentrales = $allRootNodes->reject(function($n) use ($preliminares, $finales) {
            return $preliminares->contains('id', $n->id) || $finales->contains('id', $n->id);
        })->sortBy('orden');

        $rootNodes = $preliminares->concat($capitulosCentrales)->concat($finales);

        $globalOrden = 1;
        $capIndex = 1;

        foreach ($rootNodes as $root) {
            $tituloLower = mb_strtolower($root->titulo);
            
            // Detectar si es Fase 2
            $esEspecial = str_contains($tituloLower, 'resumen ejecutivo') || 
                          str_contains($tituloLower, 'introducción') || 
                          str_contains($tituloLower, 'conclusiones') || 
                          str_contains($tituloLower, 'recomendaciones');

            if ($esEspecial) {
                // Limpiar prefijos de capítulo y dejar título limpio sin romanos
                $tituloLimpio = preg_replace('/^(Capítulo\s+[I|V|X|L|C|D|M|\d]+\.?\s*|-*\s*)/i', '', $root->titulo);
                $root->update([
                    'titulo' => trim($tituloLimpio),
                    'orden'  => $globalOrden,
                    'codigo' => null
                ]);
                $globalOrden++;
            } else {
                $numeroRomano = $romanos[$capIndex] ?? $capIndex;

                // Renumerar como Capítulo X
                $tituloLimpio = preg_replace('/^Capítulo\s+[I|V|X|L|C|D|M|\d]+\.?\s*/i', '', $root->titulo);
                $root->update([
                    'titulo' => "Capítulo {$numeroRomano}. " . ltrim($tituloLimpio, '. '),
                    'orden'  => $globalOrden,
                    'codigo' => (string)$capIndex,
                    'nivel'  => 1,
                ]);

                // Renumerar todos los subnodos, sin importar la profundidad
                $this->renumberChildren($query, $root->id, (string)$capIndex, 2);

                $capIndex++;
                $globalOrden++;
            }
        }
    }

    // Numera recursivamente los hijos como "código del padre.posición" (1.1, 1.1.1, 1.1.1.1, ...)
    private function renumberChildren(Builder $query, int $parentId, string $parentCode, int $nivel): void
    {
        $children = (clone $query)->where('parent_id', $parentId)
            ->orderBy('orden', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $index = 1;
        foreach ($children as $child) {
            $codigo = "{$parentCode}.{$index}";

            $child->update([
                'orden'  => $index,
                'codigo' => $codigo,
                'nivel'  => $nivel,
            ]);

            $this->renumberChildren($query, $child->id, $codigo, $nivel + 1);
            $index++;
        }
    }
}