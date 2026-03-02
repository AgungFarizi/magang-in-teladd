@props(['status', 'color' => null])

@php
$map = [
    'draft'               => ['bg'=>'bg-gray-100',   'text'=>'text-gray-600',   'dot'=>'bg-gray-400',   'label'=>'Draft'],
    'diajukan'            => ['bg'=>'bg-blue-100',   'text'=>'text-blue-700',   'dot'=>'bg-blue-500',   'label'=>'Diajukan'],
    'review_operator'     => ['bg'=>'bg-yellow-100', 'text'=>'text-yellow-700', 'dot'=>'bg-yellow-500', 'label'=>'Review Operator'],
    'diteruskan_manager'  => ['bg'=>'bg-indigo-100', 'text'=>'text-indigo-700', 'dot'=>'bg-indigo-500', 'label'=>'Diteruskan ke Manager'],
    'review_manager_dep'  => ['bg'=>'bg-orange-100', 'text'=>'text-orange-700', 'dot'=>'bg-orange-500', 'label'=>'Review Manajer Dep.'],
    'disetujui_manager_dep'=>['bg'=>'bg-teal-100',  'text'=>'text-teal-700',   'dot'=>'bg-teal-500',   'label'=>'Disetujui Manajer Dep.'],
    'ditolak_manager_dep' => ['bg'=>'bg-red-100',    'text'=>'text-red-700',    'dot'=>'bg-red-500',    'label'=>'Ditolak Manajer Dep.'],
    'review_manager'      => ['bg'=>'bg-purple-100', 'text'=>'text-purple-700', 'dot'=>'bg-purple-500', 'label'=>'Review Manager'],
    'diterima'            => ['bg'=>'bg-green-100',  'text'=>'text-green-700',  'dot'=>'bg-green-500',  'label'=>'Diterima'],
    'ditolak'             => ['bg'=>'bg-red-100',    'text'=>'text-red-700',    'dot'=>'bg-red-500',    'label'=>'Ditolak'],
    'aktif'               => ['bg'=>'bg-emerald-100','text'=>'text-emerald-700','dot'=>'bg-emerald-500','label'=>'Sedang Magang'],
    'selesai'             => ['bg'=>'bg-slate-100',  'text'=>'text-slate-700',  'dot'=>'bg-slate-500',  'label'=>'Selesai'],
    'dibatalkan'          => ['bg'=>'bg-gray-100',   'text'=>'text-gray-500',   'dot'=>'bg-gray-400',   'label'=>'Dibatalkan'],
    // Kehadiran / Log
    'pending'             => ['bg'=>'bg-yellow-100', 'text'=>'text-yellow-700', 'dot'=>'bg-yellow-500', 'label'=>'Pending'],
    'diverifikasi'        => ['bg'=>'bg-green-100',  'text'=>'text-green-700',  'dot'=>'bg-green-500',  'label'=>'Terverifikasi'],
    'revisi'              => ['bg'=>'bg-orange-100', 'text'=>'text-orange-700', 'dot'=>'bg-orange-500', 'label'=>'Perlu Revisi'],
    // Status periode
    'draft_periode'       => ['bg'=>'bg-gray-100',   'text'=>'text-gray-600',   'dot'=>'bg-gray-400',   'label'=>'Draft'],
    'aktif_periode'       => ['bg'=>'bg-green-100',  'text'=>'text-green-700',  'dot'=>'bg-green-500',  'label'=>'Aktif'],
    'ditutup'             => ['bg'=>'bg-red-100',    'text'=>'text-red-700',    'dot'=>'bg-red-500',    'label'=>'Ditutup'],
];
$s = $map[$status] ?? ['bg'=>'bg-gray-100','text'=>'text-gray-600','dot'=>'bg-gray-400','label'=>ucfirst($status)];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $s['bg'] }} {{ $s['text'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
    {{ $s['label'] }}
</span>
