@extends('admin.layout')

@section('content')
<table id="example" class="table table-striped table-bordered display">
    <thead>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Office</th>
            <th>Age</th>
            <th>Start date</th>
            <th>Salary</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Tiger Nixon</td>
            <td>System Architect</td>
            <td>Edinburgh</td>
            <td>61</td>
            <td>2011/04/25</td>
            <td>$320,800</td>
        </tr>
        <tr>
            <td>Garrett Winters</td>
            <td>Accountant</td>
            <td>Tokyo</td>
            <td>63</td>
            <td>2011/07/25</td>
            <td>$170,750</td>
        </tr>
        <!-- Add more rows as needed -->
    </tbody>
</table>
<script>
    $(document).ready(function() {
    // Initialize DataTable
    $('#example').DataTable({
        "paging": true,         // Enable pagination
        "searching": true,      // Enable searching
        "ordering": true,       // Enable sorting
        "info": true,           // Display table info
        "responsive": true      // Make the table responsive
    });
});

</script>
@endsection
