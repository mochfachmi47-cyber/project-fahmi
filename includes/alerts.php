<?php
/**
 * FORSAKDA 27 - Flash Alert Notifications Component
 */

$flash = get_flash();
if ($flash):
    $type = $flash['type'];
    $message = $flash['message'];

    $colors = [
        'success' => 'bg-emerald-50 border-emerald-500/30 text-emerald-800 dark:bg-emerald-950/80 dark:border-emerald-500/50 dark:text-emerald-200',
        'error'   => 'bg-rose-50 border-rose-500/30 text-rose-800 dark:bg-rose-950/80 dark:border-rose-500/50 dark:text-rose-200',
        'warning' => 'bg-amber-50 border-amber-500/30 text-amber-800 dark:bg-amber-950/80 dark:border-amber-500/50 dark:text-amber-200',
        'info'    => 'bg-sky-50 border-sky-500/30 text-sky-800 dark:bg-sky-950/80 dark:border-sky-500/50 dark:text-sky-200'
    ];

    $icons = [
        'success' => 'fa-circle-check text-emerald-600 dark:text-emerald-400',
        'error'   => 'fa-circle-xmark text-rose-600 dark:text-rose-400',
        'warning' => 'fa-triangle-exclamation text-amber-600 dark:text-amber-400',
        'info'    => 'fa-circle-info text-sky-600 dark:text-sky-400'
    ];

    $colorClass = $colors[$type] ?? $colors['info'];
    $iconClass = $icons[$type] ?? $icons['info'];
?>
    <div class="fixed top-20 right-4 z-50 max-w-md w-full auto-dismiss-alert shadow-2xl rounded-2xl border p-4 <?php echo $colorClass; ?> flex items-start gap-3 backdrop-blur-md">
        <i class="fa-solid <?php echo $iconClass; ?> text-xl mt-0.5 flex-shrink-0"></i>
        <div class="flex-1 text-sm font-medium">
            <?php echo e($message); ?>
        </div>
        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
<?php endif; ?>
