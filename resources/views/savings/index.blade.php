@extends('layouts.app')
@section('title', 'Savings')
@section('page-title', 'Savings & Goals')

@section('content')
<div x-data="{ 
    type: '{{ old('type', 'deposit') }}',
    showGoalModal: false,
    editingGoal: null
}">
    <!-- Top Stats & Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
        <div class="lg:col-span-2 bg-white border border-beige-200/60 rounded-[1.5rem] md:rounded-[2rem] p-5 md:p-7 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Total Savings Balance</p>
                <p class="text-4xl md:text-5xl font-black text-mint-700 tracking-tight">PHP {{ number_format($balance, 2) }}</p>
                <div class="flex items-center gap-4 mt-6">
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-mint-50 text-mint-700 rounded-xl border border-mint-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">{{ $streak }} Day Streak</span>
                    </div>
                    @if($streak >= 7)
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-xl border border-amber-100">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Consistency King</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="absolute right-[-20px] bottom-[-20px] opacity-[0.03] rotate-12">
                <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14H11V11H13V16ZM13 9H11V7H13V9Z"/></svg>
            </div>
        </div>

        <!-- Smart Suggestion -->
        <div class="bg-mint-700 rounded-[1.5rem] md:rounded-[2rem] p-5 md:p-7 shadow-lg shadow-mint-700/20 text-white relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-mint-200 uppercase tracking-widest mb-2">Smart Suggestion</p>
                <p class="text-xs font-bold text-mint-100 mb-4 leading-relaxed">Based on your sales today, we suggest adding this to your savings:</p>
                <p class="text-3xl font-black mb-1">PHP {{ number_format($suggestedSavings, 2) }}</p>
                <p class="text-[10px] font-bold text-mint-300">10% of today's revenue</p>
            </div>
            <div class="absolute top-0 right-0 p-4 opacity-20">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
        </div>

        <div class="bg-white border border-beige-200/60 rounded-[1.5rem] md:rounded-[2rem] p-5 md:p-7 shadow-sm">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-1">Monthly Flow</p>
                    <p class="text-xl font-black {{ $monthSavings >= 0 ? 'text-mint-700' : 'text-red-600' }}">PHP {{ number_format($monthSavings, 2) }}</p>
                </div>
                <div class="p-2 bg-beige-50 rounded-xl">
                    <svg class="w-5 h-5 text-beige-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-bold text-beige-400">Deposits</span>
                    <span class="font-black text-mint-600">+{{ number_format($totalDeposits, 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="font-bold text-beige-400">Withdrawals</span>
                    <span class="font-black text-red-600">-{{ number_format($totalWithdrawals, 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Goals Section -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-6 px-2">
            <h2 class="text-lg font-black text-mint-950 uppercase tracking-widest">Active Goals</h2>
            <button @click="showGoalModal = true" class="flex items-center gap-2 px-4 py-2 bg-mint-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-mint-800 transition-all shadow-md shadow-mint-700/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                New Goal
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @forelse($goals as $goal)
            <div class="bg-white border border-beige-200/60 rounded-[1.5rem] md:rounded-[2.5rem] p-5 md:p-7 shadow-sm hover:shadow-md transition-all relative group">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 rounded-[1.25rem] bg-mint-50 flex items-center justify-center text-mint-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click="editingGoal = {{ $goal->toJson() }}; showGoalModal = true" class="p-2 text-beige-400 hover:text-mint-600 hover:bg-mint-50 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                    </div>
                </div>

                <h4 class="text-lg font-black text-mint-950 mb-1">{{ $goal->name }}</h4>
                <p class="text-xs font-bold text-beige-400 mb-6">{{ $goal->goal_date ? 'Until ' . $goal->goal_date->format('M d, Y') : 'Ongoing Goal' }}</p>

                <div class="space-y-4">
                    <div class="flex justify-between items-end">
                        <span class="text-2xl font-black text-mint-700">PHP {{ number_format($goal->currentBalance(), 0) }}</span>
                        <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">of PHP {{ number_format($goal->target_amount, 0) }}</span>
                    </div>
                    
                    <div class="relative h-3 w-full bg-beige-100 rounded-full overflow-hidden">
                        <div class="absolute inset-y-0 left-0 bg-mint-500 rounded-full transition-all duration-1000" style="width: {{ $goal->progressPercentage() }}%"></div>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-mint-600 uppercase tracking-widest">{{ round($goal->progressPercentage()) }}% Complete</span>
                        @if($goal->goal_date)
                        <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">{{ $goal->daysRemaining }} Days Left</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="lg:col-span-3 py-16 text-center bg-white border-2 border-dashed border-beige-200 rounded-[3rem]">
                <div class="w-20 h-20 bg-beige-50 rounded-full flex items-center justify-center mx-auto mb-6 text-beige-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-sm font-black text-mint-950 uppercase tracking-widest">No Savings Goals Yet</h3>
                <p class="text-xs font-bold text-beige-400 mt-2 max-w-xs mx-auto leading-relaxed">Create a goal to start tracking progress for specific purchases or reserves.</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 md:gap-8">
        <div class="space-y-6 md:space-y-8">
            <div class="bg-white border border-beige-200/60 rounded-[1.5rem] md:rounded-[2rem] p-5 md:p-7 shadow-sm h-fit">
                <h3 class="text-sm font-black text-mint-950 uppercase tracking-widest mb-6">Add Savings Entry</h3>
                <form method="POST" action="{{ route('savings.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Assign to Goal (Optional)</label>
                        <select name="savings_goal_id" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                            <option value="">General Savings</option>
                            @foreach($goals as $goal)
                            <option value="{{ $goal->id }}">{{ $goal->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Entry Type</label>
                        <select name="type" x-model="type" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                            <option value="deposit">Add Money to Savings</option>
                            <option value="withdrawal">Use Savings Money</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Amount</label>
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Date</label>
                        <input type="date" name="transaction_date" value="{{ old('transaction_date', now()->toDateString()) }}" required class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                    </div>
                    <div x-show="type === 'withdrawal'" x-transition>
                        <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">What is this spending for?</label>
                        <input type="text" name="purpose" value="{{ old('purpose') }}" :required="type === 'withdrawal'" placeholder="Example: emergency repair, supplies" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn-mint w-full py-3 font-black uppercase tracking-widest text-xs">Save Entry</button>
                </form>
            </div>

            <!-- Future Savings Planner -->
            <div class="bg-white border border-beige-200/60 rounded-[1.5rem] md:rounded-[2rem] p-5 md:p-7 shadow-sm" x-data="{ 
                calcMode: 'projection',
                dailySaving: 10,
                goalAmount: 1000,
                startDate: '{{ now()->toDateString() }}',
                endDate: '{{ now()->addMonth()->toDateString() }}',
                formatCurrency(val) {
                    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(val);
                },
                get daysRemaining() {
                    if (!this.startDate || !this.endDate) return 0;
                    const start = new Date(this.startDate);
                    const end = new Date(this.endDate);
                    const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                    return diff > 0 ? diff : 0;
                },
                get requiredDaily() {
                    const days = this.daysRemaining;
                    return days > 0 ? this.goalAmount / days : 0;
                }
            }">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-mint-950 uppercase tracking-widest">Savings Planner</h3>
                    <div class="flex bg-beige-100 p-1 rounded-xl">
                        <button @click="calcMode = 'projection'" :class="calcMode === 'projection' ? 'bg-white text-mint-700 shadow-sm' : 'text-beige-500 hover:text-mint-600'" class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all">Projection</button>
                        <button @click="calcMode = 'goal'" :class="calcMode === 'goal' ? 'bg-white text-mint-700 shadow-sm' : 'text-beige-500 hover:text-mint-600'" class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all">Goal</button>
                    </div>
                </div>

                <!-- Projection Mode -->
                <div x-show="calcMode === 'projection'" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">If I save daily</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-beige-400">PHP</span>
                            <input type="number" x-model="dailySaving" class="w-full pl-12 pr-4 py-3 bg-beige-50/50 border border-beige-200 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-4 bg-mint-50/50 rounded-2xl border border-mint-100/50">
                            <span class="text-[10px] font-black text-mint-800 uppercase tracking-widest">Per Week</span>
                            <span class="text-sm font-black text-mint-700" x-text="formatCurrency(dailySaving * 7)"></span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-mint-50/50 rounded-2xl border border-mint-100/50">
                            <span class="text-[10px] font-black text-mint-800 uppercase tracking-widest">Per Month</span>
                            <span class="text-sm font-black text-mint-700" x-text="formatCurrency(dailySaving * 30)"></span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-mint-50/50 rounded-2xl border border-mint-100/50">
                            <span class="text-[10px] font-black text-mint-800 uppercase tracking-widest">Per Year</span>
                            <span class="text-sm font-black text-mint-700" x-text="formatCurrency(dailySaving * 365)"></span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-mint-50/50 rounded-2xl border border-mint-100/50">
                            <span class="text-[10px] font-black text-mint-800 uppercase tracking-widest">In 10 Years</span>
                            <span class="text-sm font-black text-mint-700" x-text="formatCurrency(dailySaving * 3650)"></span>
                        </div>
                    </div>
                </div>

                <!-- Goal Mode -->
                <div x-show="calcMode === 'goal'" class="space-y-6" x-cloak>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">I want to save</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-beige-400">PHP</span>
                                <input type="number" x-model="goalAmount" class="w-full pl-12 pr-4 py-3 bg-beige-50/50 border border-beige-200 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Start Date</label>
                                <input type="date" x-model="startDate" class="w-full px-4 py-3 bg-beige-50/50 border border-beige-200 rounded-2xl text-xs font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Goal Date</label>
                                <input type="date" x-model="endDate" class="w-full px-4 py-3 bg-beige-50/50 border border-beige-200 rounded-2xl text-xs font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-mint-700 rounded-[2rem] text-center shadow-lg shadow-mint-700/20">
                        <p class="text-[10px] font-black text-mint-200 uppercase tracking-widest mb-2">Required Daily Saving</p>
                        <p class="text-3xl font-black text-white" x-text="formatCurrency(requiredDaily)"></p>
                        <p class="text-[10px] font-bold text-mint-300 mt-2">
                            for <span x-text="daysRemaining"></span> days to reach <span x-text="formatCurrency(goalAmount)"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2">
            <div class="flex flex-col sm:flex-row justify-between gap-4 mb-6">
                <form method="GET" class="flex flex-col sm:flex-row w-full sm:w-auto items-stretch sm:items-center gap-3">
                    <select name="type" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                        <option value="">All Entries</option>
                        <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposits</option>
                        <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Spending</option>
                    </select>
                    <input type="date" name="from" value="{{ request('from') }}" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-xs font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                    <input type="date" name="to" value="{{ request('to') }}" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-xs font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-beige-100 text-mint-800 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-beige-200 border border-beige-200">Filter</button>
                </form>
            </div>

            <div class="bg-white border border-beige-200/60 rounded-[1.5rem] md:rounded-[2rem] overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead>
                            <tr class="bg-beige-50/50 border-b border-beige-100">
                                <th class="text-left px-7 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Date</th>
                                <th class="text-left px-7 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Goal / Type</th>
                                <th class="text-left px-7 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Purpose / Notes</th>
                                <th class="text-right px-7 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Amount</th>
                                <th class="text-right px-7 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-beige-100">
                            @forelse($transactions as $transaction)
                            <tr class="hover:bg-beige-50/50 transition-colors">
                                <td class="px-7 py-5 font-bold text-beige-500">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                <td class="px-7 py-5">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-mint-900">{{ $transaction->goal->name ?? 'General Savings' }}</span>
                                        <span class="inline-flex w-fit px-2 py-0.5 text-[8px] font-black uppercase rounded-lg tracking-widest {{ $transaction->type === 'deposit' ? 'bg-mint-100 text-mint-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $transaction->type === 'deposit' ? 'Added' : 'Spent' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-7 py-5">
                                    <p class="font-black text-mint-950">{{ $transaction->purpose ?? 'Savings deposit' }}</p>
                                    @if($transaction->notes)
                                    <p class="text-xs font-semibold text-beige-400 mt-1">{{ $transaction->notes }}</p>
                                    @endif
                                </td>
                                <td class="px-7 py-5 text-right font-black {{ $transaction->type === 'deposit' ? 'text-mint-700' : 'text-red-600' }}">
                                    {{ $transaction->type === 'deposit' ? '+' : '-' }}PHP {{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="px-7 py-5 text-right">
                                    <form method="POST" action="{{ route('savings.destroy', $transaction) }}" onsubmit="return confirm('Delete this savings entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 rounded-xl text-beige-300 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center">
                                    <p class="text-sm font-black text-mint-950 uppercase tracking-widest">No savings entries yet</p>
                                    <p class="text-xs font-bold text-beige-400 mt-2">Deposits and savings spending will appear here.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                <div class="px-8 py-6 border-t border-beige-100 bg-beige-50/20">{{ $transactions->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Goal Modal -->
    <div x-show="showGoalModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-mint-900/60 backdrop-blur-sm" @click="showGoalModal = false; editingGoal = null"></div>

            <div class="inline-block w-full max-w-lg p-6 my-4 md:p-8 md:my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[2rem] md:rounded-[3rem]">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-mint-950 uppercase tracking-widest" x-text="editingGoal ? 'Edit Savings Goal' : 'New Savings Goal'"></h3>
                    <button @click="showGoalModal = false; editingGoal = null" class="p-2 text-beige-400 hover:text-mint-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="editingGoal ? `/savings-goals/${editingGoal.id}` : '{{ route('savings-goals.store') }}'" method="POST" class="space-y-6">
                    @csrf
                    <template x-if="editingGoal">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Goal Name</label>
                        <input type="text" name="name" :value="editingGoal ? editingGoal.name : ''" required placeholder="e.g., Emergency Fund, New Equipment" class="w-full px-5 py-4 bg-beige-50/50 border border-beige-200 rounded-3xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Target Amount</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-beige-400">PHP</span>
                                <input type="number" step="0.01" name="target_amount" :value="editingGoal ? editingGoal.target_amount : ''" required class="w-full pl-12 pr-4 py-4 bg-beige-50/50 border border-beige-200 rounded-3xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Start Date</label>
                            <input type="date" name="start_date" :value="editingGoal ? editingGoal.start_date.split('T')[0] : '{{ now()->toDateString() }}'" required class="w-full px-5 py-4 bg-beige-50/50 border border-beige-200 rounded-3xl text-xs font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Goal Date (Optional)</label>
                        <input type="date" name="goal_date" :value="editingGoal && editingGoal.goal_date ? editingGoal.goal_date.split('T')[0] : ''" class="w-full px-5 py-4 bg-beige-50/50 border border-beige-200 rounded-3xl text-xs font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                    </div>

                    <template x-if="editingGoal">
                        <div>
                            <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Status</label>
                            <select name="status" class="w-full px-5 py-4 bg-beige-50/50 border border-beige-200 rounded-3xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                                <option value="active" :selected="editingGoal.status === 'active'">Active</option>
                                <option value="completed" :selected="editingGoal.status === 'completed'">Completed</option>
                                <option value="archived" :selected="editingGoal.status === 'archived'">Archived</option>
                            </select>
                        </div>
                    </template>

                    <div>
                        <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Notes</label>
                        <textarea name="notes" rows="3" :value="editingGoal ? editingGoal.notes : ''" class="w-full px-5 py-4 bg-beige-50/50 border border-beige-200 rounded-3xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500"></textarea>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row gap-4 pt-4">
                        <button type="button" @click="showGoalModal = false; editingGoal = null" class="w-full sm:flex-1 px-6 py-4 bg-beige-100 text-beige-600 text-[10px] font-black uppercase tracking-widest rounded-3xl hover:bg-beige-200 transition-all">Cancel</button>
                        <button type="submit" class="w-full sm:flex-[2] px-6 py-4 bg-mint-700 text-white text-[10px] font-black uppercase tracking-widest rounded-3xl hover:bg-mint-800 transition-all shadow-lg shadow-mint-700/20" x-text="editingGoal ? 'Update Goal' : 'Create Goal'"></button>
                    </div>
                </form>

                <template x-if="editingGoal">
                    <form :action="`/savings-goals/${editingGoal.id}`" method="POST" class="mt-4" onsubmit="return confirm('Are you sure you want to delete this goal?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-6 py-4 border-2 border-red-100 text-red-500 text-[10px] font-black uppercase tracking-widest rounded-3xl hover:bg-red-50 transition-all">Delete Goal</button>
                    </form>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
