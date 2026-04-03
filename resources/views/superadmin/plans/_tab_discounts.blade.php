<div class="rounded-2xl border-2 overflow-hidden" style="background:var(--sa-bg);border-color:var(--sa-border);">
    @if($discounts->count() > 0)
        <div class="overflow-x-auto">
            <table class="disc-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Discount</th>
                        <th>Applies To</th>
                        <th>Valid Period</th>
                        <th>Uses</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discounts as $d)
                        @php
                            $statusClass = match($d->status_label) {
                                'Active'    => 'sb-success',
                                'Scheduled' => 'sb-warning',
                                default     => 'sb-danger',
                            };
                        @endphp
                        <tr>
                            {{-- Code --}}
                            <td>
                                <code class="px-2 py-1 rounded text-xs font-bold"
                                      style="background:rgba(0,48,135,.08);color:var(--sa-accent);">
                                    {{ $d->code }}
                                </code>
                            </td>

                            {{-- Name --}}
                            <td>
                                <div class="font-semibold">{{ $d->name }}</div>
                                @if($d->tenant)
                                    <div class="text-xs" style="color:var(--sa-muted);">
                                        <i class="fas fa-building mr-1"></i>{{ $d->tenant->name }}
                                    </div>
                                @endif
                            </td>

                            {{-- Discount value --}}
                            <td>
                                <span class="font-bold text-base" style="color:var(--sa-success);">
                                    {{ $d->formatted_value }}
                                    @if($d->type === 'percentage')
                                        <span class="text-xs font-normal" style="color:var(--sa-muted);">off</span>
                                    @endif
                                </span>
                                <div class="text-xs" style="color:var(--sa-muted);">{{ ucfirst($d->type) }}</div>
                            </td>

                            {{-- Plans / actions --}}
                            <td class="text-xs" style="color:var(--sa-muted);">
                                @if($d->applicable_plans)
                                    <div>{{ implode(', ', array_map('ucfirst', $d->applicable_plans)) }}</div>
                                @else
                                    <span>All plans</span>
                                @endif
                                @if($d->applicable_actions)
                                    <div class="text-xs opacity-70">{{ implode(', ', $d->applicable_actions) }}</div>
                                @endif
                            </td>

                            {{-- Valid period --}}
                            <td class="text-xs" style="color:var(--sa-muted);">
                                @if($d->valid_from || $d->valid_until)
                                    {{ $d->valid_from?->format('M d, Y') ?? '—' }} → {{ $d->valid_until?->format('M d, Y') ?? '—' }}
                                @else
                                    <span>No limit</span>
                                @endif
                            </td>

                            {{-- Uses --}}
                            <td>
                                <span class="font-semibold">{{ $d->uses_count }}</span>
                                <span class="text-xs" style="color:var(--sa-muted);">
                                    / {{ $d->max_uses ?? '∞' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="status-badge {{ $statusClass }}">{{ $d->status_label }}</span>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="flex items-center gap-1">
                                    <button onclick="openEditDiscount({{ $d->id }})" class="btn btn-outline btn-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <form action="{{ route('superadmin.plans.discounts.destroy', $d) }}" method="POST"
                                          onsubmit="return confirm('Delete discount {{ $d->code }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- Hidden data for JS population --}}
                                <div id="disc-data-{{ $d->id }}" class="hidden"
                                     data-id="{{ $d->id }}"
                                     data-name="{{ $d->name }}"
                                     data-code="{{ $d->code }}"
                                     data-type="{{ $d->type }}"
                                     data-value="{{ $d->value }}"
                                     data-plans="{{ json_encode($d->applicable_plans ?? []) }}"
                                     data-actions="{{ json_encode($d->applicable_actions ?? []) }}"
                                     data-tenant="{{ $d->tenant_id ?? '' }}"
                                     data-valid-from="{{ $d->valid_from?->format('Y-m-d') ?? '' }}"
                                     data-valid-until="{{ $d->valid_until?->format('Y-m-d') ?? '' }}"
                                     data-max-uses="{{ $d->max_uses ?? '' }}"
                                     data-min-price="{{ $d->minimum_price ?? '' }}"
                                     data-active="{{ $d->is_active ? '1' : '0' }}"
                                     data-update-url="{{ route('superadmin.plans.discounts.update', $d) }}">
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-14 text-center">
            <i class="fas fa-percent text-5xl mb-4" style="color:var(--sa-muted);opacity:.3;"></i>
            <p style="color:var(--sa-muted);" class="mb-3">No discount codes yet.</p>
            <button onclick="openModal('modal-new-discount')" class="btn btn-gold">
                <i class="fas fa-plus"></i> Create First Discount
            </button>
        </div>
    @endif
</div>
