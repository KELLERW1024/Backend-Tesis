<?php

namespace App\Console\Commands;

use App\Models\PlanNode;
use App\Services\StructureService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RenumberStructures extends Command
{
    protected $signature = 'structure:renumber {--user-plan= : Renumerar solo este user_plan_id} {--dry-run : Mostrar los cambios sin guardarlos}';

    protected $description = 'Renumera las estructuras (plan_nodes) de los usuarios en todos sus niveles';

    public function handle(StructureService $structureService): int
    {
        $structures = PlanNode::whereNotNull('user_plan_id')
            ->when($this->option('user-plan'), fn ($q, $id) => $q->where('user_plan_id', $id))
            ->select('plan_id', 'user_plan_id')
            ->distinct()
            ->get();

        $dryRun = (bool) $this->option('dry-run');
        $totalChanges = 0;

        foreach ($structures as $structure) {
            DB::beginTransaction();

            $before = $this->snapshot($structure->user_plan_id);
            $structureService->recalculateStructure($structure->plan_id, $structure->user_plan_id);
            $after = $this->snapshot($structure->user_plan_id);

            $changes = [];
            foreach ($after as $id => $node) {
                if ($before[$id] !== $node) {
                    $changes[] = [$id, $before[$id]['codigo'] ?? '(vacío)', $node['codigo'] ?? '(vacío)', $node['titulo']];
                }
            }

            $dryRun ? DB::rollBack() : DB::commit();

            $this->info("user_plan {$structure->user_plan_id} (plan {$structure->plan_id}): " . count($changes) . ' nodos cambiados');
            if ($changes) {
                $this->table(['id', 'código anterior', 'código nuevo', 'título'], $changes);
            }
            $totalChanges += count($changes);
        }

        $this->info(($dryRun ? '[SIMULACIÓN] ' : '') . "Total de nodos cambiados: {$totalChanges}");

        return self::SUCCESS;
    }

    private function snapshot(int $userPlanId): array
    {
        return PlanNode::where('user_plan_id', $userPlanId)
            ->get(['id', 'codigo', 'orden', 'nivel', 'titulo'])
            ->keyBy('id')
            ->map(fn ($n) => $n->only(['codigo', 'orden', 'nivel', 'titulo']))
            ->all();
    }
}
