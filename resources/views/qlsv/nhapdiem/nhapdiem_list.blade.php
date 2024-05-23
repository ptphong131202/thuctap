@extends('layouts.admin-vue')

@section('content')
<nhapdiem-danhsach-box permissions="{{$permissions}}"></nhapdiem-danhsach-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush