<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Specific Element</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div id="journal-entries">
    	<h1>Accounting Journal Entries</h1>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Account</th>
                    <th>Description</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Reference Number</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2023-04-06</td>
                    <td>Cash</td>
                    <td>Initial Capital</td>
                    <td>$10,000</td>
                    <td></td>
                    <td>INV-001</td>
                    <td>Initial investment by the owner</td>
                </tr>
                <tr>
                    <td>2023-04-07</td>
                    <td>Equipment</td>
                    <td>Purchase of Equipment</td>
                    <td>$5,000</td>
                    <td></td>
                    <td>PO-123</td>
                    <td>Purchased office equipment from XYZ Corp</td>
                </tr>
                <tr>
                    <td>2023-04-07</td>
                    <td>Cash</td>
                    <td>Payment for Equipment</td>
                    <td></td>
                    <td>$5,000</td>
                    <td>CHK-456</td>
                    <td>Paid for equipment via check</td>
                </tr>
                <tr>
                    <td>2023-04-08</td>
                    <td>Rent Expense</td>
                    <td>Monthly Rent</td>
                    <td>$1,000</td>
                    <td></td>
                    <td>INV-002</td>
                    <td>Monthly rent payment for office space</td>
                </tr>
                <tr>
                    <td>2023-04-08</td>
                    <td>Cash</td>
                    <td>Payment for Rent</td>
                    <td></td>
                    <td>$1,000</td>
                    <td>CHK-789</td>
                    <td>Paid rent via check</td>
                </tr>
            </tbody>
        </table>
    </div>
    <button onclick="printElement('journal-entries')">Print Journal Entries</button>

    <script>
        function printElement(elementId) {
            var element = document.getElementById(elementId);
            var newWindow = window.open('', '', 'width=800,height=600');
            newWindow.document.write('<html><head><title>Print</title>');
            newWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid black; padding: 8px; text-align: left; } th { background-color: #f2f2f2; }</style>');
            newWindow.document.write('</head><body>');
            newWindow.document.write(element.innerHTML);
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();
        }
    </script>
</body>
</html>
