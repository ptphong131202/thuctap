@extends('layouts.admin-vue')

@section('content')
<nhatky-box permissions="{{$permissions}}"></nhatky-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
