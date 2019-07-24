<div class="table-responsive">
    <table class="table table-bordered" id="dataTableOrderHistory" width="100%" cellspacing="0">
        <thead>                
                <th>Order #</th>
                <th>Order Date</th>
                <th>Combo/Service</th>
                <th>Price($)</th>
                <th>Charged($)</th>
                <th>Payment Type</th>                
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">123</td>
                <td class="text-center">12-12-2019</td>
                <td>Advance Website</td>
                <td class="text-right">199</td>
                <td class="text-right">199</td>
                <td class="text-center">Visa</td>
            </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
 $(document).ready(function() {
    $('#dataTableOrderHistory').DataTable();
});
</script>