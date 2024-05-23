@extends('layouts.admin-vue')

@section('content')
<dotthichitiet-box :qd="'{{ $quyetDinh ?? false }}'" :dt_id="'{{ $dotthi->dt_id }}'" :dt_ten="'{{ $dotthi->dt_ten }}'" :parent_url="'{{ $parentUrl }}'"></dotthichitiet-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
