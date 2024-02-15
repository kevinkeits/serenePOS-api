<div class="grid grid-cols-1">
    <div>
		<h2 class="text-sm font-semibold text-gray-700 mt-4">Reminder</h2>
		<div class="border border-transparent sm:border-gray-200 rounded-lg sm:p-2 mt-2">
      		<div class="grid">
                <table class="table">
                    <tbody>
                        <tr id="dash_stockapproval" style="display:none">
                            <td>Approval Penambahan Stok</td>
                            <td>: <span id="lblApproval" onclick="loadMenu('stockapproval','Persetujuan')" style="cursor:pointer;color:blue">0</span></td>
                        </tr>
                        <tr id="dash_selling" style="display:none">
                            <td>Transaksi Belum Kirim 3 Hari</td>
                            <td>: <span id="lblConfirmed" onclick="loadMenu('selling','Penjualan')" style="cursor:pointer;color:blue">0</span></td>
                        </tr>
                        <tr id="dash_b2b" style="display:none">
                            <td>B2B Belum Bayar 30 Hari</td>
                            <td>: <span id="lblUnpaid" onclick="loadMenu('b2b','B2B')" style="cursor:pointer;color:blue">0</span></td>
                        </tr>
                        <tr id="dash_chat" style="display:none">
                            <td>Pesan Belum Dijawab</td>
                            <td>: <span id="lblUnread" onclick="loadMenu('chat','Pesan')" style="cursor:pointer;color:blue">0</span></td>
                        </tr>
                    </tbody>
                </table>
			</div>
      	</div>
    </div>
	<div>
		<h2 class="text-sm font-semibold text-gray-700 mb-2 mt-4">Penjualan Per Cabang</h2>
		<div class="border border-transparent sm:border-gray-200 rounded-lg sm:p-2 mt-2">
			<div class="grid grid-cols-2">
				<div>
					<canvas id="chartPenjualan"></canvas>
				</div>
			</div>
		</div>

		<h2 class="text-sm font-semibold text-gray-700 mb-2 mt-4">Produk Terlaris</h2>
		<div class="border border-transparent sm:border-gray-200 rounded-lg sm:p-2 mt-2">
			<div class="grid grid-cols-2">
				<div>
					<canvas id="chartProduk"></canvas>
				</div>
			</div>
		</div>
  	</div>
  </div>
</div>
<script>
    doFetch('dashboard/getTransaction','');
    doFetch('dashboard/getProduct','');
    doFetch('dashboard/getNotification','');

    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    function onCompleteFetchNotification(data) {
        $('#lblApproval').html(data.stockApproval);
        $('#lblConfirmed').html(data.confirmedTransaction);
        $('#lblUnpaid').html(data.waitingPayment);
        $('#lblUnread').html(data.unreadMessage);

        if (('.tagMenu_stockapproval').length > 0) $('#dash_stockapproval').show();
        if (('.tagMenu_selling').length > 0) $('#dash_selling').show();
        if (('.tagMenu_b2b').length > 0) $('#dash_b2b').show();
        if (('.tagMenu_chats').length > 0) $('#dash_chat').show();
    }

    function onCompleteFetchTransaction(data){
        var dataList = [];
        for (var index = 0; index < data.dataList.length; index++) {
            dataList.push(
                {
                    label: data.dataList[index]['name'],
                    data: data.dataList[index]['value'],
                    backgroundColor: getRandomColor(),
                    borderWidth: 1
                }
            )
        }
        var chartPenjualan = new Chart(document.getElementById('chartPenjualan').getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.dateList,
                datasets: dataList
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: false,
                    }
                }
            },
        });
    }

    function onCompleteFetchProduct(data){
        var dataList = [];
        var labelList = [];
        var colorList = [];
        for (var index = 0; index < data.length; index++) {
            dataList.push(
                data[index].TotalSales
            );
            labelList.push(
                data[index].Product
            );
            colorList.push(
                getRandomColor()
            );
        }
        var chartProduct = new Chart(document.getElementById('chartProduk').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelList,
                datasets: [{
                    label: "Total",
                    data: dataList,
                    backgroundColor: colorList,
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: false,
                    }
                }
            },
        });
    }
	
</script>