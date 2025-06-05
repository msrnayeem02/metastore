<div class="table-responsive">
    <table id="{{ $id ?? 'dataTable' }}" class="table table-striped table-bordered {{ $class ?? '' }}">
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
        @if($footers ?? false)
            <tfoot>
                <tr>
                    @foreach ($footers as $footer)
                        <th>{{ $footer }}</th>
                    @endforeach
                </tr>
            </tfoot>
        @endif
    </table>
</div>
