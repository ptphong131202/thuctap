@extends('layouts.admin-vue')

@section('content')
<khoadaotao-box permissions="{{$permissions}}"></khoadaotao-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush