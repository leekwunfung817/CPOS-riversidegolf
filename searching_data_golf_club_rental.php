<?php 

    session_start();
if (!isset($_SESSION["management"])) {
     ?>
    <script type="text/javascript">
        alert('請使用您的員工帳號重新登錄\nPlease login with your staff account again');
        window.location.href = "./";
    </script>
    <?php
    die();
}
 ?>
<?php 

require_once 'account_variable.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
echo "con err";
    die("Connection failed: " . $conn->connect_error);
}


$sql = "
	SELECT `name`, `deposit`, `rental-fee` FROM `golf-club-price` order by name desc;
";
$prices_list = array();
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
			$prices_list[ $row['name'] ] = $row;
    }
}

 ?>
<?php 




// INSERT INTO `golf-club-rental-record`(`bay`) VALUES ('VIP');

// UPDATE `golf-club-rental-record` 
// 	SET `returned`='1'
// 	WHERE `golf-club-seq`='';

// SELECT `golf-club-seq`, `start-dt`, `bay`, `returned` 
// FROM `golf-club-rental-record` 
// WHERE `returned`='0';

// SELECT `golf-club-seq`, `start-dt`, `bay`, `returned` 
// FROM `golf-club-rental-record` 
// WHERE `returned`='1';









require_once './position_list.php';

 ?>

<h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>
<style type="text/css">
	body {
		font-family: "Segoe UI", Tahoma, sans-serif;
		background: linear-gradient(180deg, #f4f9ff 0%, #ffffff 60%);
		color: #16263b;
		margin: 14px;
	}

	h1, h2, h3 {
		margin-top: 8px;
		margin-bottom: 8px;
		line-height: 1.25;
	}

	a {
		color: #0d4a9e;
		text-decoration: none;
	}

	a:hover {
		text-decoration: underline;
	}

	table {
		border-collapse: collapse;
		background: #ffffff;
	}

	th {
		border: 2px solid #9fb7d1;
		padding: 8px 10px;
		vertical-align: middle;
		background: #eaf3ff;
		text-align: center;
	}

	td {
		vertical-align: middle;
		border: 2px solid #b8cce2;
		padding: 7px 9px;
		white-space: nowrap;
		text-align: center;
	}

	button,
	input[type="submit"] {
		border: 1px solid #4d79a8;
		background: #2c74bf;
		color: #ffffff;
		padding: 6px 10px;
		border-radius: 6px;
		cursor: pointer;
	}

	button:hover,
	input[type="submit"]:hover {
		background: #245f9c;
	}

	button:disabled,
	input[type="submit"]:disabled {
		opacity: 0.6;
		cursor: not-allowed;
	}

	input[type="text"],
	input[type="number"],
	input[type="date"],
	select {
		padding: 5px 6px;
		border: 1px solid #98b4d1;
		border-radius: 5px;
		background: #ffffff;
	}

	#gcp_table,
	#rs_table,
	#rental_history_table {
		box-shadow: 0 2px 8px rgba(30, 68, 112, 0.08);
	}

	#gcp_status,
	#rs_status,
	#rh_status_text {
		background: #eef6ff;
		border: 1px solid #c7ddf6;
		padding: 7px 9px;
		border-radius: 6px;
	}

	#rental-history-controls > div,
	#rs_table,
	#gcp_table {
		align-items: center;
	}

	iframe#receipt_printing_buffer {
		border: 2px solid #bed3e8;
		border-radius: 8px;
		background: #ffffff;
	}

	@media (max-width: 1100px) {
		body {
			margin: 8px;
		}

		td, th {
			padding: 6px;
		}
	}
</style>
<!-- 
<style type="text/css">
	td {
		vertical-align: text-top;
		border-style: solid;
		border-width: 2px;
		white-space: nowrap;
	}
	table {
		
	}
</style>
 -->
<table style="height: 10px;width: 100%;">
	<tr>
		<th style="vertical-align: text-top;" colspan="3">
			<h1 style="vertical-align: text-top;"	>租賃管理<br>Rental Management</h1>
		</th>
	</tr>
	<tr>
		<th style="vertical-align: text-top;text-align: left;width: 50%;">

			<details style="margin-top: 12px;max-width: 780px;">
				<summary style="cursor: pointer;font-weight: bold;padding: 8px 10px;border: 1px solid #c7d8ee;background: #eef6ff;border-radius: 6px;">
                    租賃價格管理 Rental Price Management (展開/收合 Expand/Collapse)</summary>
				<div style="margin-top: 8px;border: 1px solid #ccc;padding: 10px;background: #f8fbff;">
				<div style="display: flex;flex-wrap: wrap;gap: 8px;align-items: end;">
					<div>
						<label>名稱 Name</label><br>
						<input type="text" id="gcp_name" placeholder="e.g. Wood" style="min-width: 160px;" />
					</div>
					<div>
						<label>按金 Deposit</label><br>
						<input type="number" id="gcp_deposit" step="0.01" min="0" style="width: 130px;" />
					</div>
					<div>
						<label>租賃費 Rental Fee</label><br>
						<input type="number" id="gcp_rental_fee" step="0.01" min="0" style="width: 130px;" />
					</div>
					<div>
						<button type="button" id="gcp_add_btn">新增 Add</button>
						<button type="button" id="gcp_update_btn">更新 Update</button>
						<button type="button" id="gcp_cancel_edit_btn">取消編輯 Cancel Edit</button>
					</div>
				</div>
				<div id="gcp_status" style="margin-top: 8px;font-weight: bold;"></div>

				<table style="width: 100%;margin-top: 8px;" id="gcp_table">
					<thead>
						<tr>
							<th>名稱 Name</th>
							<th>按金 Deposit</th>
							<th>租賃費 Rental Fee</th>
							<th style="width: 220px;">操作 Actions</th>
						</tr>
					</thead>
					<tbody id="gcp_tbody">
						<tr><td colspan="4">Loading...</td></tr>
					</tbody>
				</table>
				</div>
			</details>

			<script type="text/javascript">
			const gcpState = {
				editMode: false,
				originalName: ''
			};

			function gcpEscapeHtml(text) {
				if (text === null || text === undefined) {
					return '';
				}
				return String(text)
					.replace(/&/g, '&amp;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&#039;');
			}

			function gcpSetStatus(message, isError) {
				const el = document.getElementById('gcp_status');
				el.style.color = isError ? '#a10000' : '#0a5500';
				el.textContent = message;
			}

			function gcpSetEditMode(isEdit, originalName) {
				gcpState.editMode = isEdit;
				gcpState.originalName = originalName || '';
				document.getElementById('gcp_add_btn').disabled = isEdit;
				document.getElementById('gcp_update_btn').disabled = !isEdit;
				document.getElementById('gcp_cancel_edit_btn').disabled = !isEdit;
			}

			function gcpClearForm() {
				document.getElementById('gcp_name').value = '';
				document.getElementById('gcp_deposit').value = '';
				document.getElementById('gcp_rental_fee').value = '';
			}

			function gcpCollectForm() {
				return {
					name: document.getElementById('gcp_name').value.trim(),
					deposit: document.getElementById('gcp_deposit').value.trim(),
					rental_fee: document.getElementById('gcp_rental_fee').value.trim()
				};
			}

			async function gcpCallApi(action, payload) {
				const params = new URLSearchParams();
				params.set('action', action);
				if (payload) {
					for (const key of Object.keys(payload)) {
						params.set(key, payload[key]);
					}
				}

				const response = await fetch('./golf-club-price-api.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
					},
					body: params.toString()
				});

				return response.json();
			}

			function gcpRenderRows(records) {
				const tbody = document.getElementById('gcp_tbody');
				if (!records || records.length === 0) {
					tbody.innerHTML = '<tr><td colspan="4">No price records found.</td></tr>';
					return;
				}

				let html = '';
				for (const row of records) {
					html += '<tr>';
					html += '<td>' + gcpEscapeHtml(row.name) + '</td>';
					html += '<td>' + gcpEscapeHtml(row.deposit) + '</td>';
					html += '<td>' + gcpEscapeHtml(row.rental_fee) + '</td>';
					html += '<td>'
						+ '<button type="button" class="gcp_edit_btn" data-name="' + gcpEscapeHtml(row.name) + '" data-deposit="' + gcpEscapeHtml(row.deposit) + '" data-rental-fee="' + gcpEscapeHtml(row.rental_fee) + '">編輯 Edit</button> '
						+ '<button type="button" class="gcp_delete_btn" data-name="' + gcpEscapeHtml(row.name) + '">刪除 Delete</button>'
						+ '</td>';
					html += '</tr>';
				}
				tbody.innerHTML = html;

				const editBtns = document.querySelectorAll('.gcp_edit_btn');
				for (const btn of editBtns) {
					btn.addEventListener('click', function () {
						document.getElementById('gcp_name').value = this.getAttribute('data-name') || '';
						document.getElementById('gcp_deposit').value = this.getAttribute('data-deposit') || '';
						document.getElementById('gcp_rental_fee').value = this.getAttribute('data-rental-fee') || '';
						gcpSetEditMode(true, this.getAttribute('data-name') || '');
						gcpSetStatus('Editing: ' + (this.getAttribute('data-name') || ''), false);
					});
				}

				const deleteBtns = document.querySelectorAll('.gcp_delete_btn');
				for (const btn of deleteBtns) {
					btn.addEventListener('click', async function () {
						const name = this.getAttribute('data-name') || '';
						if (!confirm('Delete price row: ' + name + ' ?')) {
							return;
						}
						try {
							const data = await gcpCallApi('delete', { name: name });
							if (!data.success) {
								gcpSetStatus(data.message || 'Delete failed', true);
								return;
							}
							gcpSetStatus('Deleted: ' + name, false);
							gcpSetEditMode(false, '');
							gcpClearForm();
							await gcpLoadList();
						} catch (e) {
							gcpSetStatus('Delete request failed', true);
						}
					});
				}
			}

			async function gcpLoadList() {
				const tbody = document.getElementById('gcp_tbody');
				tbody.innerHTML = '<tr><td colspan="4">Loading...</td></tr>';
				try {
					const data = await gcpCallApi('list', {});
					if (!data.success) {
						gcpSetStatus(data.message || 'Failed to load price list', true);
						tbody.innerHTML = '<tr><td colspan="4">Load failed.</td></tr>';
						return;
					}
					gcpRenderRows(data.records || []);
				} catch (e) {
					gcpSetStatus('Load request failed', true);
					tbody.innerHTML = '<tr><td colspan="4">Load failed.</td></tr>';
				}
			}

			document.getElementById('gcp_add_btn').addEventListener('click', async function () {
				const form = gcpCollectForm();
				if (!form.name || !form.deposit || !form.rental_fee) {
					gcpSetStatus('Please fill Name, Deposit and Rental Fee', true);
					return;
				}

				try {
					const data = await gcpCallApi('add', form);
					if (!data.success) {
						gcpSetStatus(data.message || 'Add failed', true);
						return;
					}
					gcpSetStatus('Added: ' + form.name, false);
					gcpClearForm();
					await gcpLoadList();
				} catch (e) {
					gcpSetStatus('Add request failed', true);
				}
			});

			document.getElementById('gcp_update_btn').addEventListener('click', async function () {
				if (!gcpState.editMode) {
					gcpSetStatus('Please click Edit on a row first', true);
					return;
				}

				const form = gcpCollectForm();
				if (!form.name || !form.deposit || !form.rental_fee) {
					gcpSetStatus('Please fill Name, Deposit and Rental Fee', true);
					return;
				}

				try {
					const payload = {
						original_name: gcpState.originalName,
						name: form.name,
						deposit: form.deposit,
						rental_fee: form.rental_fee
					};
					const data = await gcpCallApi('edit', payload);
					if (!data.success) {
						gcpSetStatus(data.message || 'Update failed', true);
						return;
					}
					gcpSetStatus('Updated: ' + payload.original_name + ' -> ' + payload.name, false);
					gcpSetEditMode(false, '');
					gcpClearForm();
					await gcpLoadList();
				} catch (e) {
					gcpSetStatus('Update request failed', true);
				}
			});

			document.getElementById('gcp_cancel_edit_btn').addEventListener('click', function () {
				gcpSetEditMode(false, '');
				gcpClearForm();
				gcpSetStatus('Edit cancelled', false);
			});

			gcpSetEditMode(false, '');
			gcpLoadList();
			</script>












			<div style="margin-top: 18px;border: 1px solid #c9d9ef;padding: 10px;background: #f7fbff;max-width: 900px;">
				<h2 style="margin: 0 0 8px 0;">租賃 提交表格 Rental Submit form (Multiple Items)</h2>
				<div style="margin-bottom: 8px;display: flex;gap: 8px;align-items: center;">
					<button type="button" id="rs_select_all">全選 Select All</button>
					<button type="button" id="rs_unselect_all">取消全選 Unselect All</button>
				</div>
				<table style="width: 100%;" id="rs_table">
					<thead>
						<tr>
							<th style="width: 90px;">選擇 Select</th>
							<th style="width: 180px;">名稱 Name</th>
							<th style="width: 180px;">球道 Bay</th>
							<th style="width: 120px;">數量 Quantity</th>
							<th style="width: 120px;">按金 Deposit</th>
							<th style="width: 120px;">租賃費 Rental Fee</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($prices_list as $key => $price_set) { ?>
						<tr>
							<td>
								<input type="checkbox" class="rs_item_check" />
							</td>
							<td class="rs_name"><?php echo htmlspecialchars($price_set['name']); ?></td>
							<td>
								<select class="rs_bay" style="width: 120px;">
									<?php foreach ($position_list_ as $key1 => $sublist) { foreach ($sublist as $key2 => $value) { ?>
									<option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
									<?php }} ?>
								</select>
							</td>
							<td>
								<input type="number" class="rs_quantity" min="1" max="999" value="1" style="width: 90px;" />
							</td>
							<td><?php echo htmlspecialchars($price_set['deposit']); ?></td>
							<td><?php echo htmlspecialchars($price_set['rental-fee']); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<br>
				<div style="margin-bottom: 8px;display: flex;gap: 8px;align-items: center;">
					<button type="button" id="rs_submit_btn">提交所選項目 Submit Selected Items</button>
				</div>
			</div>

			<script type="text/javascript">
			function rsGetElement(id) {
				return document.getElementById(id);
			}

			function rsSetStatus(message, isError) {
				const statusEl = rsGetElement('rs_status');
				if (!statusEl) {
					return;
				}
				statusEl.style.color = isError ? '#a10000' : '#0a5500';
				statusEl.textContent = message;
			}

			function rsGetRows() {
				return Array.from(document.querySelectorAll('#rs_table tbody tr'));
			}

			function rsCollectSelectedItems() {
				const rows = rsGetRows();
				const items = [];
				for (const row of rows) {
					const check = row.querySelector('.rs_item_check');
					if (!check || !check.checked) {
						continue;
					}
					const nameEl = row.querySelector('.rs_name');
					const bayEl = row.querySelector('.rs_bay');
					const qtyEl = row.querySelector('.rs_quantity');

					const name = (nameEl ? nameEl.textContent : '').trim();
					const bay = bayEl ? bayEl.value : '';
					const quantity = qtyEl ? parseInt(qtyEl.value, 10) : 0;

					if (!name || !bay || isNaN(quantity) || quantity < 1) {
						continue;
					}

					items.push({
						name: name,
						bay: bay,
						quantity: quantity
					});
				}
				return items;
			}

			const rsSelectAllBtn = rsGetElement('rs_select_all');
			if (rsSelectAllBtn) {
				rsSelectAllBtn.addEventListener('click', function () {
				const checks = document.querySelectorAll('.rs_item_check');
				for (const check of checks) {
					check.checked = true;
				}
				});
			}

			const rsUnselectAllBtn = rsGetElement('rs_unselect_all');
			if (rsUnselectAllBtn) {
				rsUnselectAllBtn.addEventListener('click', function () {
				const checks = document.querySelectorAll('.rs_item_check');
				for (const check of checks) {
					check.checked = false;
				}
				});
			}

			const rsSubmitBtn = rsGetElement('rs_submit_btn');
			if (rsSubmitBtn) {
				rsSubmitBtn.addEventListener('click', async function () {
				const items = rsCollectSelectedItems();
				if (items.length === 0) {
					rsSetStatus('Please select at least one valid item.', true);
					return;
				}

				rsSetStatus('Submitting rental items...', false);
				try {
					const response = await fetch('./golf-club-rental-submit-api.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json;charset=UTF-8'
						},
						body: JSON.stringify({ items: items })
					});

					const data = await response.json();
					if (!data.success) {
						rsSetStatus(data.message || 'Submit failed', true);
						return;
					}

					let message = 'Submitted. Inserted rows: ' + data.inserted_rows;
					if (data.validation_errors && data.validation_errors.length > 0) {
						message += ' | Some rows skipped: ' + data.validation_errors.join(' ; ');
					}
					rsSetStatus(message, false);

					if (data.print_seqs && data.print_seqs.length > 0) {
						window.location.href = '?print_rent_batch=1&print_seq_list=' + encodeURIComponent(data.print_seqs.join(','));
						return;
					}

					if (data.print_seq) {
						window.location.href = '?seq=' + encodeURIComponent(data.print_seq) + '&print_rent';
						return;
					}

					setTimeout(function () {
						window.location.reload();
					}, 800);
				} catch (e) {
					rsSetStatus('Submit request failed', true);
				}
				});
			}
			</script>













		</th>
		<th>
			<?php 


			 ?>


<iframe id="receipt_printing_buffer" style="width: 100%;height: 600px;">
</iframe>


			<a onclick="
			    const oIframe = document.getElementById('receipt_printing_buffer');
			    oIframe.contentWindow.print();
			" 
			style="
			    color: blue;
			    background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
			    padding: 30px;
			    width: 90%;
			    border-radius: 30px;
			    display: block;
			    text-align: center;
			    cursor: pointer;
			">
			    列印收據 Print Receipt
			</a>

<script type="text/javascript">


function comfirm_and_print(
	title,
	id,
	bay,
	date,
	deposit,
	fee,
	name,
	returned,
	quantity
) {

  var msg = '';
  var printing = '<h1>白石高球練習場</h1>';
  printing += '<h1>'+title+'</h1>';
  <?php 
      if (isset($_SESSION['name'])&&isset($_SESSION['name2'])) {
   ?>
  printing += '<i style="text-align: left;">On-Duty: <?php echo $_SESSION['name'].' - '.$_SESSION['name2']; ?> </i><br>';
  <?php 
      }
   ?>

  printing += '<div style="text-align: right;">Tel: 27771813</div>';
  printing += '<div style="text-align: right;">RIVERSIDE Whitehead Golf Club</div>';
  printing += '<i style="text-align: center;"><hr></i>';

  sourceTxt = id;
  sourceName = '序列號 Seq.';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;font-size: 1.2em;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = bay;
  sourceName = '球道 Bay';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;font-size: 1.2em;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = date;
  sourceName = '開始時間 Rental time';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = deposit;
  sourceName = '按金 Deposit';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = fee;
  sourceName = '租賃費 Rental Fee';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = name;
  sourceName = '球桿 Rental Content';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = returned;
  sourceName = '歸還狀態 Return State';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }


  sourceTxt = quantity;
  sourceName = '數量 Quantity';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }



  
  // if (confirm(msg)) {
    const oIframe = document.getElementById('receipt_printing_buffer');
    oIframe.contentWindow.document.open();
    oIframe.contentWindow.document.write(printing);
    oIframe.contentWindow.document.close();
    oIframe.contentWindow.print();
  // }
}   

</script>


<?php 


$staff = '';
if (isset($_SESSION['name'])) {
	$staff = $_SESSION['name'];
}

$latest_rent_seq = 0;

function build_golf_club_rental_where_by_seq($seq) {
	$seq = (int) $seq;
	if ($seq <= 0) {
		return '';
	}

	return " 
		AND `bay`=(
			SELECT `b`.`bay`
			FROM `golf-club-rental-record` `b`
			WHERE `b`.`golf-club-seq`='".$seq."'
		)
		AND `name`=(
			SELECT `b`.`name`
			FROM `golf-club-rental-record` `b`
			WHERE `b`.`golf-club-seq`='".$seq."'
		)
		AND `deposit`=(
			SELECT `b`.`deposit`
			FROM `golf-club-rental-record` `b`
			WHERE `b`.`golf-club-seq`='".$seq."'
		)
		AND `rental-fee`=(
			SELECT `b`.`rental-fee`
			FROM `golf-club-rental-record` `b`
			WHERE `b`.`golf-club-seq`='".$seq."'
		)
		AND DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')=( 
			SELECT DATE_FORMAT(`b`.`start-dt`, '%Y-%m-%d %H:%i:%s') 
			FROM `golf-club-rental-record` `b`
			WHERE `b`.`golf-club-seq`='".$seq."' 
		) ";
}

function output_golf_club_rental_print_scripts($conn, $seq, $delay_ms) {
	$seq = (int) $seq;
	$delay_ms = (int) $delay_ms;
	if ($seq <= 0) {
		return;
	}

	$where_addition = build_golf_club_rental_where_by_seq($seq);
	$sql = "
		SELECT 
			MAX(`golf-club-seq`) `golf-club-seq`, 
			`start-dt`,
			MAX(`bay`) `bay`, 
			GROUP_CONCAT(`returned` SEPARATOR ', ') `returned`, 
			MAX(`name`) `name`,
			SUM(`deposit`) `deposit`,
			SUM(`rental-fee`) `rental-fee`,
			COUNT(*) `count`
		FROM `golf-club-rental-record` 
		WHERE `returned` IS NULL
		$where_addition
		group by `bay`, `name`, `deposit`, `rental-fee`, DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')
		order by `start-dt` desc
		limit 1;
	";

	$result = $conn->query($sql);
	if (!$result || $result->num_rows === 0) {
		return;
	}

	while ($row = $result->fetch_assoc()) {
		echo '<script type="text/javascript">';
		echo 'setTimeout(function () {';
		echo 'comfirm_and_print(';
		echo json_encode('高爾夫球桿<br>租賃收據<br>(職員)', JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode($row['golf-club-seq'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode($row['bay'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode($row['start-dt'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode('HKD $' . $row['deposit'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode('HKD $' . $row['rental-fee'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode($row['name'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode(($row['returned'] == null ? '尚未歸還' : '已經歸還'), JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode($row['count'], JSON_UNESCAPED_UNICODE);
		echo ');';
		echo '}, ' . $delay_ms . ');';

		echo 'setTimeout(function () {';
		echo 'comfirm_and_print(';
		echo json_encode('高爾夫球桿<br>租賃收據<br>(客戶)', JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode($row['golf-club-seq'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode($row['bay'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode($row['start-dt'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode('HKD $' . $row['deposit'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode('HKD $' . $row['rental-fee'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode($row['name'], JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode(($row['returned'] == null ? '尚未歸還' : '已經歸還'), JSON_UNESCAPED_UNICODE) . ',';
		echo json_encode($row['count'], JSON_UNESCAPED_UNICODE);
		echo ');';
		echo '}, ' . ($delay_ms + 1000) . ');';
		echo '</script>';
	}
}


if ( isset($_GET['golf-club-name']) && isset($_GET['bay']) && isset($_GET['rent']) ) {

	$price_set = $prices_list[ $_GET['golf-club-name'] ];
	$quantity = 1;
	if (isset($_GET['quantity'])) {
		$quantity = $_GET['quantity'];
	}
	for ($i=0; $i < $quantity; $i++) {
		$sql = "
		INSERT INTO `golf-club-rental-record`(
			`bay`,
			`name`,
			`deposit`,
			`rental-fee`,
			`src`
		) 
		VALUES (
			'".$_GET['bay']."'
			,'".$price_set['name']."'
			,'".$price_set['deposit']."'
			,'".$price_set['rental-fee']."'
			,'".$staff."'
		);";
		$result = $conn->query($sql);
		$latest_rent_seq = (int) $conn->insert_id;


	}
}


if (isset($_GET['print_rent_batch']) && isset($_GET['print_seq_list'])) {
	$print_seq_list = array();
	foreach (explode(',', $_GET['print_seq_list']) as $raw_seq) {
		$seq_value = (int) trim($raw_seq);
		if ($seq_value > 0) {
			$print_seq_list[$seq_value] = $seq_value;
		}
	}

	$delay_ms = 0;
	foreach ($print_seq_list as $seq_value) {
		output_golf_club_rental_print_scripts($conn, $seq_value, $delay_ms);
		$delay_ms += 2500;
	}
} elseif (isset($_GET['seq']) && isset($_GET['print_rent'])) {
	output_golf_club_rental_print_scripts($conn, $_GET['seq'], 0);
} elseif (isset($_GET['golf-club-name']) && isset($_GET['bay']) && isset($_GET['rent']) && $latest_rent_seq > 0) {
	output_golf_club_rental_print_scripts($conn, $latest_rent_seq, 0);
}

if ( isset($_GET['seq']) && isset($_GET['return']) ) {
	$sql = "
	UPDATE `golf-club-rental-record` 
		SET `returned`=CURRENT_TIMESTAMP
		WHERE 1=1
		AND `returned` is null
		AND `bay`=(
			SELECT `bay`
			FROM `golf-club-rental-record` `b`
			WHERE `golf-club-seq`='".$_GET['seq']."'
		)
		AND `name`=(
			SELECT `name`
			FROM `golf-club-rental-record` `b`
			WHERE `golf-club-seq`='".$_GET['seq']."'
		)
		AND `deposit`=(
			SELECT `deposit`
			FROM `golf-club-rental-record` `b`
			WHERE `golf-club-seq`='".$_GET['seq']."'
		)
		AND `rental-fee`=(
			SELECT `rental-fee`
			FROM `golf-club-rental-record` `b`
			WHERE `golf-club-seq`='".$_GET['seq']."'
		)
		AND `start-dt`=(
			SELECT `b`.`start-dt`
			FROM `golf-club-rental-record` `b`
			WHERE `golf-club-seq`='".$_GET['seq']."'
		)
		;
	";
	$result = $conn->query($sql);

		if (isset($_GET['seq'])) {
			$where_addition = " 
			AND `bay`=(
				SELECT `b`.`bay`
				FROM `golf-club-rental-record` `b`
				WHERE `b`.`golf-club-seq`='".$_GET['seq']."'
			)
			AND `name`=(
				SELECT `b`.`name`
				FROM `golf-club-rental-record` `b`
				WHERE `b`.`golf-club-seq`='".$_GET['seq']."'
			)
			AND `deposit`=(
				SELECT `b`.`deposit`
				FROM `golf-club-rental-record` `b`
				WHERE `b`.`golf-club-seq`='".$_GET['seq']."'
			)
			AND `rental-fee`=(
				SELECT `b`.`rental-fee`
				FROM `golf-club-rental-record` `b`
				WHERE `b`.`golf-club-seq`='".$_GET['seq']."'
			)
			AND DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')=( 
				SELECT DATE_FORMAT(`b`.`start-dt`, '%Y-%m-%d %H:%i:%s') 
				FROM `golf-club-rental-record` `b`
				WHERE `b`.`golf-club-seq`='".$_GET['seq']."' 
			) ";
		}

		$sql = "
			SELECT 
				MAX(`golf-club-seq`) `golf-club-seq`, 
				`start-dt`,
				MAX(`bay`) `bay`, 
				MAX(`returned`) `returned`, 
				MAX(`name`) `name`,
				SUM(`deposit`) `deposit`,
				SUM(`rental-fee`) `rental-fee`,
				COUNT(*) `count`
			FROM `golf-club-rental-record` 
			WHERE `returned` IS NOT NULL
			$where_addition
			group by `bay`, `name`, `deposit`, `rental-fee`, DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')
			order by `start-dt` desc
			limit 1;
		";


	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	    while ($row = $result->fetch_assoc()) {
			?>
			<script type="text/javascript">
				comfirm_and_print(
					'歸還收據<br>(職員)',
					'<?php echo $row['golf-club-seq']; ?>',
					'<?php echo $row['bay']; ?>',
					'<?php echo $row['start-dt']; ?>',

					'HKD $<?php echo $row['deposit']; ?>',
					'HKD $<?php echo $row['rental-fee']; ?>',
					'<?php echo $row['name']; ?>',
					'<?php echo ($row['returned']==null ? '尚未歸還':'已經歸還'); ?>'
				);
				setTimeout(function () {
					comfirm_and_print(
						'歸還收據<br>(客戶)',
						'<?php echo $row['golf-club-seq']; ?>',
						'<?php echo $row['bay']; ?>',
						'<?php echo $row['start-dt']; ?>',

						'HKD $<?php echo $row['deposit']; ?>',
						'HKD $<?php echo $row['rental-fee']; ?>',
						'<?php echo $row['name']; ?>',
						'<?php echo ($row['returned']==null ? '尚未歸還':'已經歸還'); ?>'
					);
				},1000);


			</script>
			<?php

	    }
	}
}

if ( isset($_GET['seq']) && isset($_GET['remove']) ) {
	$sql = "
	DELETE FROM `golf-club-rental-record`
	WHERE 1=1
	AND `returned` is null
	AND `bay`=(
		SELECT `bay`
		FROM `golf-club-rental-record` `b`
		WHERE `b`.`golf-club-seq`='".$_GET['seq']."'
	)
	AND `name`=(
		SELECT `name`
		FROM `golf-club-rental-record` `b`
		WHERE `b`.`golf-club-seq`='".$_GET['seq']."'
	)
	AND `deposit`=(
		SELECT `deposit`
		FROM `golf-club-rental-record` `b`
		WHERE `b`.`golf-club-seq`='".$_GET['seq']."'
	)
	AND `rental-fee`=(
		SELECT `rental-fee`
		FROM `golf-club-rental-record` `b`
		WHERE `b`.`golf-club-seq`='".$_GET['seq']."'
	)
	AND `start-dt`=(
		SELECT `b`.`start-dt`
		FROM `golf-club-rental-record` `b`
		WHERE `b`.`golf-club-seq`='".$_GET['seq']."'
	)
	;
	";
	$result = $conn->query($sql);
}

$complexArray = array();

$sql = "
	SELECT 

		`golf-club-seq`, 
		`start-dt`, 
		`bay`, 
		`returned`,

		`name`,
		`deposit`,
		`rental-fee`

	FROM `golf-club-rental-record` 
	WHERE `returned` IS NULL;
";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
    	if (!isset($complexArray[$row['bay']])) {
    		$complexArray[$row['bay']] = array();
    	}
    	$complexArray[$row['bay']][] = $row;
    }
}



 ?>
		</th>
	</tr>
	<tr>
		<th style="width: 30%;" colspan="3">
			<h2>
				未還 Not yet returned
			</h2>
			<?php 

$sql = "
	SELECT 
		DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s') `start-dt`, 
		MAX(`golf-club-seq`) `golf-club-seq-max`, 
		GROUP_CONCAT(`golf-club-seq` SEPARATOR ', ') `golf-club-seq`, 
		count(*) `quantity`,
		`bay`, 
		GROUP_CONCAT(`returned` SEPARATOR ', ') `returned`, 
		MAX(`name`) `name`,
		SUM(`deposit`) `deposit`,
		SUM(`rental-fee`) `rental-fee`
	FROM `golf-club-rental-record` 
	WHERE `returned` IS NULL
	group by `bay`, `name`, `deposit`, `rental-fee`, DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')
	order by `bay` asc,`start-dt` desc;
";
$result = $conn->query($sql);

echo "<table>";
    	echo "<tr>";
    	
    	echo "<td style=\"width: 200px;\">";
    	echo '序列號<br>Sequence Number';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo '租賃 開始日期時間<br>Rental Timestamp';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "球道<br>Bay";
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo '名稱<br>Name';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo '按金<br>Deposit';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "租賃費<br>Rental Fee";
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "數量<br>Quantity";
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "列印<br>Print";
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "歸還<br>Return";
    	echo "</td>";

	    echo "<td style=\"width: 200px;\">";
	    echo "移除<br>Remove";
	    echo "</td>";

    	echo "</tr>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
    	echo "<tr>";

    	echo "<td>";
    	echo $row['golf-club-seq-max'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['start-dt'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['bay'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['name'];
    	echo "</td>";

    	echo "<td>$";
    	echo $row['deposit'];
    	echo "</td>";

    	echo "<td>$";
    	echo $row['rental-fee'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['quantity'];
    	echo "</td>";

    	echo "<td>";
		echo "<a href=\"?seq=".$row['golf-club-seq-max']."&print_rent\">列印 Print</a>";
    	echo "</td>";

    	echo "<td>";
		echo "<a href=\"?seq=".$row['golf-club-seq-max']."&return\">歸還 Return</a>";
    	echo "</td>";

	    echo "<td>";
		echo "<a onclick=\"return confirm('Confirm remove this not-yet-returned row?');\" href=\"?seq=".$row['golf-club-seq-max']."&remove\">移除 Remove</a>";
	    echo "</td>";



    	echo "</tr>";
    }
}
echo "</table>";

			 ?>

		</th>
	</tr>
	<tr>
		<th>
		</th>
		<th>
		</th>
	</tr>
</table>
<?php

echo "<hr>";
?>

<h1>歸還歷史 Returned History</h1>

<div id="rental-history-controls" style="margin-bottom: 10px;">
	<div style="display: flex;flex-wrap: wrap;gap: 8px;align-items: end;">
		<div>
			<label>狀態 Status</label><br>
			<select id="rh_status">
				<option value="returned">已歸還 Returned</option>
				<option value="not_returned">未歸還 Not Returned</option>
				<option value="all">全部 All</option>
			</select>
		</div>
		<div>
			<label>球道 Bay</label><br>
			<select id="rh_bay">
				<option value="">All</option>
				<?php
				foreach ($position_list_ as $key1 => $sublist) {
					foreach ($sublist as $key2 => $value) {
						?>
						<option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
						<?php
					}
				}
				?>
			</select>
		</div>
		<div>
			<label>球桿名稱 Club</label><br>
			<select id="rh_club_name">
				<option value="">All</option>
				<?php
				foreach ($prices_list as $name => $price_set) {
					?>
					<option value="<?php echo htmlspecialchars($price_set['name']); ?>"><?php echo htmlspecialchars($price_set['name']); ?></option>
					<?php
				}
				?>
			</select>
		</div>
		<div>
			<label>關鍵字 Keyword</label><br>
			<input type="text" id="rh_keyword" placeholder="Seq / Bay / Name / Staff" style="min-width: 210px;" />
		</div>
		<div>
			<label>開始日期 From</label><br>
			<input type="date" id="rh_date_from" />
		</div>
		<div>
			<label>結束日期 To</label><br>
			<input type="date" id="rh_date_to" />
		</div>
		<div>
			<label>排序 Sort</label><br>
			<select id="rh_sort_by">
				<option value="start_dt">Rental Timestamp</option>
				<option value="returned">Return Timestamp</option>
				<option value="seq">Sequence Number</option>
				<option value="bay">Bay</option>
				<option value="name">Club Name</option>
				<option value="deposit">Deposit</option>
				<option value="rental_fee">Rental Fee</option>
			</select>
		</div>
		<div>
			<label>方向 Direction</label><br>
			<select id="rh_sort_dir">
				<option value="desc">DESC</option>
				<option value="asc">ASC</option>
			</select>
		</div>
		<div>
			<label>每頁 Rows/Page</label><br>
			<select id="rh_page_size">
				<option value="10">10</option>
				<option value="20" selected>20</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select>
		</div>
		<div>
			<button type="button" id="rh_apply">搜尋 Search</button>
			<button type="button" id="rh_reset">重設 Reset</button>
		</div>
	</div>
</div>

<div id="rh_status_text" style="font-weight: bold;margin-bottom: 6px;"></div>

<div style="margin-top: 10px;display: flex;flex-wrap: wrap;gap: 6px;align-items: center;">
	<button type="button" id="rh_first"><< 首頁 First</button>
	<button type="button" id="rh_prev">< 上一頁 Prev</button>
	<span id="rh_pages"></span>
	<button type="button" id="rh_next">下一頁 Next ></button>
	<button type="button" id="rh_last">末頁 Last >></button>
	<span style="margin-left: 12px;">跳轉 Go to page:</span>
	<input type="number" id="rh_go_page" min="1" value="1" style="width: 90px;" />
	<button type="button" id="rh_go_btn">前往 GO</button>
</div>

<table id="rental_history_table" style="width: 100%;">
	<thead>
		<tr>
			<th style="width: 120px;">序列號<br>Sequence Number</th>
			<th style="width: 180px;">租賃開始日期時間<br>Rental Timestamp</th>
			<th style="width: 120px;">球道<br>Bay</th>
			<th style="width: 180px;">歸還日期時間<br>Return Timestamp</th>
			<th style="width: 150px;">高爾夫球桿<br>Golf Club Rental</th>
			<th style="width: 120px;">按金<br>Deposit</th>
			<th style="width: 120px;">租賃費<br>Rental Fee</th>
			<th style="width: 160px;">職員 Staff</th>
		</tr>
	</thead>
	<tbody id="rental_history_tbody">
		<tr>
			<td colspan="8">Loading...</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">

const rentalHistoryState = {
	page: 1,
	pageSize: 20,
	status: 'returned',
	bay: '',
	clubName: '',
	keyword: '',
	dateFrom: '',
	dateTo: '',
	sortBy: 'start_dt',
	sortDir: 'desc',
	totalPages: 1,
	totalRecords: 0
};

function escapeHtml(text) {
	if (text === null || text === undefined) {
		return '';
	}
	return String(text)
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/"/g, '&quot;')
		.replace(/'/g, '&#039;');
}

function buildRentalHistoryUrl() {
	const params = new URLSearchParams();
	params.set('page', rentalHistoryState.page);
	params.set('page_size', rentalHistoryState.pageSize);
	params.set('status', rentalHistoryState.status);
	params.set('keyword', rentalHistoryState.keyword);
	params.set('bay', rentalHistoryState.bay);
	params.set('club_name', rentalHistoryState.clubName);
	params.set('date_from', rentalHistoryState.dateFrom);
	params.set('date_to', rentalHistoryState.dateTo);
	params.set('sort_by', rentalHistoryState.sortBy);
	params.set('sort_dir', rentalHistoryState.sortDir);
	return './golf-club-rental-record-api.php?' + params.toString();
}

function renderHistoryRows(records) {
	const tbody = document.getElementById('rental_history_tbody');
	if (!records || records.length === 0) {
		tbody.innerHTML = '<tr><td colspan="8">No records found.</td></tr>';
		return;
	}

	let html = '';
	for (const row of records) {
		html += '<tr>';
		html += '<td>' + escapeHtml(row.seq) + '</td>';
		html += '<td>' + escapeHtml(row.start_dt) + '</td>';
		html += '<td>' + escapeHtml(row.bay) + '</td>';
		html += '<td>' + escapeHtml(row.returned_dt || '-') + '</td>';
		html += '<td>' + escapeHtml(row.name) + '</td>';
		html += '<td>$' + escapeHtml(row.deposit) + '</td>';
		html += '<td>$' + escapeHtml(row.rental_fee) + '</td>';
		html += '<td>' + escapeHtml(row.staff || '-') + '</td>';
		html += '</tr>';
	}
	tbody.innerHTML = html;
}

function renderPagination(meta) {
	rentalHistoryState.totalPages = meta.total_pages || 1;
	rentalHistoryState.totalRecords = meta.total_records || 0;

	const statusText = document.getElementById('rh_status_text');
	statusText.innerHTML = 'Total: ' + rentalHistoryState.totalRecords
		+ ' | Page: ' + meta.page + ' / ' + meta.total_pages
		+ ' | Current page rows: ' + (meta.current_page_count || 0);

	const pagesWrap = document.getElementById('rh_pages');
	let btnHtml = '';
	const pages = (meta.page_window && meta.page_window.pages) ? meta.page_window.pages : [];
	for (const p of pages) {
		const isCurrent = (p === meta.page);
		btnHtml += '<button type="button" data-page="' + p + '" class="rh_page_btn"'
			+ (isCurrent ? ' style="font-weight: bold;background: #dceeff;"' : '')
			+ '>' + p + '</button>';
	}
	pagesWrap.innerHTML = btnHtml;

	document.getElementById('rh_first').disabled = !meta.has_prev;
	document.getElementById('rh_prev').disabled = !meta.has_prev;
	document.getElementById('rh_next').disabled = !meta.has_next;
	document.getElementById('rh_last').disabled = !meta.has_next;

	document.getElementById('rh_go_page').value = meta.page;

	const pageBtns = document.querySelectorAll('.rh_page_btn');
	for (const btn of pageBtns) {
		btn.addEventListener('click', function () {
			const targetPage = parseInt(this.getAttribute('data-page'), 10);
			if (!isNaN(targetPage)) {
				rentalHistoryState.page = targetPage;
				loadRentalHistory();
			}
		});
	}
}

async function loadRentalHistory() {
	const tbody = document.getElementById('rental_history_tbody');
	tbody.innerHTML = '<tr><td colspan="8">Loading...</td></tr>';

	const url = buildRentalHistoryUrl();
	try {
		const response = await fetch(url);
		const data = await response.json();

		if (!data.success) {
			tbody.innerHTML = '<tr><td colspan="8">API error.</td></tr>';
			return;
		}

		renderHistoryRows(data.records);
		renderPagination(data.meta);
	} catch (e) {
		tbody.innerHTML = '<tr><td colspan="8">Network or parsing error.</td></tr>';
	}
}

function syncFiltersToState(resetPage) {
	rentalHistoryState.status = document.getElementById('rh_status').value;
	rentalHistoryState.bay = document.getElementById('rh_bay').value;
	rentalHistoryState.clubName = document.getElementById('rh_club_name').value;
	rentalHistoryState.keyword = document.getElementById('rh_keyword').value.trim();
	rentalHistoryState.dateFrom = document.getElementById('rh_date_from').value;
	rentalHistoryState.dateTo = document.getElementById('rh_date_to').value;
	rentalHistoryState.sortBy = document.getElementById('rh_sort_by').value;
	rentalHistoryState.sortDir = document.getElementById('rh_sort_dir').value;
	rentalHistoryState.pageSize = parseInt(document.getElementById('rh_page_size').value, 10) || 20;
	if (resetPage) {
		rentalHistoryState.page = 1;
	}
}

document.getElementById('rh_apply').addEventListener('click', function () {
	syncFiltersToState(true);
	loadRentalHistory();
});

document.getElementById('rh_reset').addEventListener('click', function () {
	document.getElementById('rh_status').value = 'returned';
	document.getElementById('rh_bay').value = '';
	document.getElementById('rh_club_name').value = '';
	document.getElementById('rh_keyword').value = '';
	document.getElementById('rh_date_from').value = '';
	document.getElementById('rh_date_to').value = '';
	document.getElementById('rh_sort_by').value = 'start_dt';
	document.getElementById('rh_sort_dir').value = 'desc';
	document.getElementById('rh_page_size').value = '20';
	syncFiltersToState(true);
	loadRentalHistory();
});

document.getElementById('rh_first').addEventListener('click', function () {
	rentalHistoryState.page = 1;
	loadRentalHistory();
});

document.getElementById('rh_prev').addEventListener('click', function () {
	if (rentalHistoryState.page > 1) {
		rentalHistoryState.page -= 1;
		loadRentalHistory();
	}
});

document.getElementById('rh_next').addEventListener('click', function () {
	if (rentalHistoryState.page < rentalHistoryState.totalPages) {
		rentalHistoryState.page += 1;
		loadRentalHistory();
	}
});

document.getElementById('rh_last').addEventListener('click', function () {
	rentalHistoryState.page = rentalHistoryState.totalPages;
	loadRentalHistory();
});

document.getElementById('rh_go_btn').addEventListener('click', function () {
	let p = parseInt(document.getElementById('rh_go_page').value, 10);
	if (isNaN(p) || p < 1) {
		p = 1;
	}
	if (p > rentalHistoryState.totalPages) {
		p = rentalHistoryState.totalPages;
	}
	rentalHistoryState.page = p;
	loadRentalHistory();
});

document.getElementById('rh_keyword').addEventListener('keydown', function (e) {
	if (e.key === 'Enter') {
		e.preventDefault();
		syncFiltersToState(true);
		loadRentalHistory();
	}
});

syncFiltersToState(true);
loadRentalHistory();

</script>

<?php
$conn->close();
?>
