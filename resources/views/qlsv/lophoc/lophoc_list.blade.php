@extends('layouts.admin-vue')

@section('content')
<lophoc-box permissions="{{$permissions}}"></lophoc-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
