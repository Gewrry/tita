@extends('layouts.app')
@section('title', 'Customers')
@section('page-title', 'Customer Directory')

@section('content')
<div x-data="{ createModalOpen: false }">
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <form method="GET" class="flex-1 max-w-md w-full">
        <div class="relative group">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-beige-400 group-focus-within:text-mint-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email or phone..."
                   class="w-full pl-11 pr-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
        </div>
    </form>
    <button type="button" @click="createModalOpen = true" class="btn-mint shadow-lg shadow-mint-900/10 whitespace-nowrap w-full sm:w-auto justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        New Customer
    </button>
</div>

<div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[800px]">
            <thead>
                <tr class="bg-beige-50/50 border-b border-beige-100">
                    <th class="text-left px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Customer Profile</th>
                    <th class="text-left px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Contact Information</th>
                    <th class="text-center px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Activity</th>
                    <th class="text-right px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Manage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-beige-100">
                @forelse($customers as $customer)
                <tr class="hover:bg-beige-50/50 transition-colors group" x-data="{ editModalOpen: false, soaModalOpen: false, deleteModalOpen: false }">
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-mint-50 border border-mint-100 flex items-center justify-center text-sm font-black text-mint-600 transition-transform group-hover:scale-105">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('customers.show', $customer) }}" class="block font-black text-mint-900 hover:text-mint-600 transition-colors truncate">{{ $customer->name }}</a>
                                @if($customer->address)
                                <p class="text-[11px] font-bold text-beige-400 truncate max-w-[200px] mt-0.5 uppercase tracking-tight">{{ $customer->address }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <p class="font-bold text-mint-800">{{ $customer->email ?? '—' }}</p>
                        <p class="text-xs font-bold text-beige-400 mt-0.5">{{ $customer->phone ?? '' }}</p>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-xl bg-beige-100 text-[11px] font-black text-mint-600 uppercase tracking-widest">
                            {{ $customer->invoices_count }} Invoices
                        </span>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <button type="button" @click.prevent="soaModalOpen = true" class="p-2 rounded-xl bg-white border border-beige-200 text-mint-600 hover:bg-mint-50 hover:border-mint-200 hover:text-mint-700 transition-all shadow-sm" title="Statement of Account">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </button>
                            <button type="button" @click.prevent="editModalOpen = true" class="p-2 rounded-xl bg-white border border-beige-200 text-beige-500 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition-all shadow-sm" title="Edit Profile">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" @click.prevent="deleteModalOpen = true" class="p-2 rounded-xl bg-white border border-beige-200 text-red-400 hover:bg-red-50 hover:border-red-200 hover:text-red-700 transition-all shadow-sm" title="Remove Customer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>

                        @include('customers.partials.soa-modal')
                        @include('customers.partials.row-modals')
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-20 text-center">
                        <div class="w-20 h-20 bg-beige-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-beige-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-sm font-black text-mint-900 uppercase tracking-widest">No customers found</p>
                        <p class="text-xs font-bold text-beige-400 mt-1">Start by adding your first client to the system</p>
                        <button type="button" @click="createModalOpen = true" class="text-mint-600 hover:text-mint-700 font-black text-xs mt-4 inline-block uppercase tracking-widest border-b-2 border-mint-500/30">Add Customer →</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="px-6 py-5 border-t border-beige-100 bg-beige-50/30">
        {{ $customers->links() }}
    </div>
    @endif
    @include('customers.partials.create-modal')
</div>
@endsection

@push('scripts')
<style>
    /* Custom pagination styling to match the theme */
    .pagination {
        @apply flex gap-2;
    }
    .page-link {
        @apply w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black transition-all border border-transparent shadow-sm;
    }
    .active .page-link {
        @apply bg-mint-500 text-white shadow-mint-900/20;
    }
    .page-link:not(.active) {
        @apply bg-white text-mint-800 hover:border-beige-200;
    }
</style>
@endpush

