@extends('layouts.admin-vue')

@section('content')
<dotxettotnghiep-dotthichitiet-box :dt_id="'{{ $dotthi->dt_id }}'" :dt_ten="'{{ $dotthi->dt_ten }}'"></dotxettotnghiep-dotthichitiet-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
