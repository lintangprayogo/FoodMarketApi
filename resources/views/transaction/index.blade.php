<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
         
          <div class="bg-white">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th class="border px-6 py-4">ID</th>
                        <th class="border px-6 py-4">Food</th>
                        <th class="border px-6 py-4">User</th>
                        <th class="border px-6 py-4">Quantity</th>
                        <th class="border px-6 py-4">Total</th>
                        <th class="border px-6 py-4">Status</th>
                        <th class="border px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                   @forelse ($transactions as $transaction)
                   <tr>
                   <td class="border px-6 py-4">{{$transaction->id}}</td>
                   <td class="border px-6 py-4">{{$transaction->food->name}}</td>
                   <td class="border px-6 py-4">{{$transaction->user->name}}</td>
                   <td class="border px-6 py-4">{{$transaction->quantity}}</td>
                   <td class="border px-6 py-4">{{$transaction->total}}</td>
                   <td class="border px-6 py-4">{{$transaction->status}}</td>
                   <td class="border px-6 py-4">
                    <a href="{{ route('transaction.show', $transaction->id) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mx-2 rounded">
                        View
                    </a>
                    <form action="{{ route('transaction.destroy', $transaction->id) }}" method="POST" class="inline-block">
                        {!! method_field('delete') . csrf_field() !!}
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 mx-2 rounded inline-block">
                            Delete
                        </button>
                    </form>
                   </td>
                   </tr>
                   @empty
                       <tr>
                           <td colspan="7" class="border text-center p-5">
                               There Is No Item
                           </td>
                       </tr>
                   @endforelse
                </tbody>
            </table>
            <div class="text-center mt-5">
              {{ $transactions->links() }}

            </div>
        </div>
        </div>
    </div>
</x-app-layout>
