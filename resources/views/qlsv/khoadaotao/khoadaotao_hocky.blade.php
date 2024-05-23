@extends('layouts.admin-vue')

@section('content')
<khoadaotao-edit-box parent_url={{ $parentUrl }} :kdt_id="{{ $id }}"></khoadaotao-edit-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
