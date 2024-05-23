@extends('layouts.admin-vue')

@section('content')
<monhoc-box excel="{{ route('mon-hoc.import-excel'); }}" permissions="{{$permissions}}"></monhoc-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush