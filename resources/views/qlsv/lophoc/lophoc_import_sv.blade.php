@extends('layouts.admin-vue')

@section('content')
<importsv-box :lh_id="{{ $id }}" :router="'{{ route('lop-hoc.chi-tiet', $id); }}'"></importsv-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush