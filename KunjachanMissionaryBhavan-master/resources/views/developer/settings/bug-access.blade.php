<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Bug Report Access Control</h2></x-slot>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th class="text-center">Access</th></tr></thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->role }}</td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('developer.settings.bug-access.update',$u) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="bug_report_enabled" value="{{ $u->bug_report_enabled?0:1 }}">
                                    <button class="btn btn-sm btn-{{ $u->bug_report_enabled?'success':'outline-secondary' }}" type="submit">{{ $u->bug_report_enabled? 'Enabled':'Enable' }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())<div class="card-footer">{{ $users->links() }}</div>@endif
    </div>
</x-app-layout>
