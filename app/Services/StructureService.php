<?php

namespace App\Services;

use App\Models\PlanNode;
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
                ?? $this->getNextOrder($parentId, $data['plan_id']);

            // Desplazar elementos posteriores
            PlanNode::where('plan_id', $data['plan_id'])
                ->where('parent_id', $parentId)
                ->where('orden', '>=', $orden)
                ->increment('orden');

            $nivel = $this->calculateLevel($parentId);

            return PlanNode::create([
                'user_plan_id' => $data['user_plan_id'] ?? null,
                'plan_id'      => $data['plan_id'],
                'parent_id'    => $parentId,
                'titulo'       => $data['titulo'],
                'orden'        => $orden,
                'objective'    => $data['objective'] ?? null,
                'nivel'        => $nivel,
            ]);
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
    // - Este método permite: 1.4 → 1.2
    // pero también:
    //Capítulo I → Capítulo II o 1.4 → hijo de 1.2

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

            /*
             * CASO 1:
             * Se mueve dentro del mismo padre
             */
            if ($oldParentId == $newParentId) {

                // Se mueve hacia arriba
                if ($newOrder < $oldOrder) {

                    PlanNode::where('plan_id', $node->plan_id)
                        ->where('parent_id', $oldParentId)
                        ->where('id', '!=', $node->id)
                        ->whereBetween('orden', [
                            $newOrder,
                            $oldOrder - 1
                        ])
                        ->increment('orden');

                }

                // Se mueve hacia abajo
                elseif ($newOrder > $oldOrder) {

                    PlanNode::where('plan_id', $node->plan_id)
                        ->where('parent_id', $oldParentId)
                        ->where('id', '!=', $node->id)
                        ->whereBetween('orden', [
                            $oldOrder + 1,
                            $newOrder
                        ])
                        ->decrement('orden');
                }

            }

            /*
             * CASO 2:
             * Se mueve a otro padre
             */
            else {

                // Cerramos el hueco que deja
                PlanNode::where('plan_id', $node->plan_id)
                    ->where('parent_id', $oldParentId)
                    ->where('orden', '>', $oldOrder)
                    ->decrement('orden');

                // Abrimos espacio en el nuevo padre
                PlanNode::where('plan_id', $node->plan_id)
                    ->where('parent_id', $newParentId)
                    ->where('orden', '>=', $newOrder)
                    ->increment('orden');
            }

            $node->update([
                'parent_id' => $newParentId,
                'orden'     => $newOrder,
                'nivel'     => $this->calculateLevel($newParentId),
            ]);

            return $node->fresh();
        });
    }

    // EVITAR CICLOS 
    //No puedes permitir:

    // 1.6
    //     └── 1.6.1
    //             └── 1.6 No puedes permitir:

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
        int $planId
    ): int {

        return (
            PlanNode::where('plan_id', $planId)
                ->where('parent_id', $parentId)
                ->max('orden')
            ?? 0
        ) + 1;
    }

    // ELIMINAR 
    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {

            $node = PlanNode::lockForUpdate()
                ->findOrFail($id);

            $parentId = $node->parent_id;
            $orden = $node->orden;

            // Primero eliminar todo el árbol
            $this->deleteChildren($node->id);

            $node->delete();

            // Cerrar hueco
            PlanNode::where('plan_id', $node->plan_id)
                ->where('parent_id', $parentId)
                ->where('orden', '>', $orden)
                ->decrement('orden');
        });
    }

    // DELETE CHILDREN
    private function deleteChildren(int $parentId): void
    {
        $children = PlanNode::where('parent_id', $parentId)->get();

        foreach ($children as $child) {

            $this->deleteChildren($child->id);

            $child->delete();
        }
    }
    



}



