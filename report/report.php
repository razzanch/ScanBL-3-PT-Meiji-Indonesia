<?php
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_scanbl3";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if product and lot are set in session
if (!isset($_SESSION['selected_product']) || !isset($_SESSION['selected_lot'])) {
    die("No product or lot selected");
}

$product_id = $_SESSION['selected_product'];
$lot_number = $_SESSION['selected_lot'];

// Query untuk mendapatkan data sesuai dengan spesifikasi
$query = "
    SELECT 
        am.rss_code AS Barcode,
        am.product AS Product,
        am.jam_code AS JamCode,
        ap.date AS Date,
        ap.counter AS Counter,
        ap.no_lot AS NoLot
    FROM add_product AS ap
    INNER JOIN add_master AS am 
        ON ap.add_master_id_master = am.id_master
    WHERE ap.no_lot = ? AND ap.add_master_id_master = ?
    ORDER BY ap.date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $lot_number, $product_id);
$stmt->execute();
$result = $stmt->get_result();

$report_data = [];
while ($row = $result->fetch_assoc()) {
    $report_data[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Report</title>
    <link rel="stylesheet" href="report.css">
    <script src="../services/export/jspdf.umd.js"></script>
<script src="../services/export/jspdf.plugin.autotable.min.js"></script>
<script src="../services/export/xlsx.full.min.js"></script>

</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <img src="../assets/meijiUNMASK.png" alt="Meiji Logo">
            <div class="report-title">
                <h1>Product Report</h1>
                <p>Product: <?php echo htmlspecialchars($report_data[0]['Product'] ?? 'N/A'); ?></p>
                <p>Lot Number: <?php echo htmlspecialchars($lot_number); ?></p>
                <p>Print Date: <?php echo date("Y-m-d H:i:s"); ?></p> <!-- Menampilkan tanggal cetak -->
            </div>
        </div>
        
        <div class="report-details">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>RSS-CODE</th>
                        <th>Product</th>
                        <th>Jam Code</th>
                        <th>Date</th>
                        <th>Counter</th>
                        <th>NO. LOT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($report_data as $row): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo !empty($row['Barcode']) ? htmlspecialchars($row['Barcode']) : "-"; ?></td>
                        <td><?php echo !empty($row['Product']) ? htmlspecialchars($row['Product']) : "-"; ?></td>
                        <td><?php echo !empty($row['JamCode']) ? htmlspecialchars($row['JamCode']) : "-"; ?></td>
                        <td><?php echo !empty($row['Date']) ? htmlspecialchars($row['Date']) : "-"; ?></td>
                        <td><?php echo !empty($row['Counter']) ? htmlspecialchars($row['Counter']) : "-"; ?></td>
                        <td><?php echo !empty($row['NoLot']) ? htmlspecialchars($row['NoLot']) : "-"; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="report-summary">
            <p>Total Records: <?php echo count($report_data); ?></p>
        </div>

        <div class="report-actions">
        <button onclick="window.print()">
   Print 
   <span style="font-size: 45px; display: inline-block; vertical-align: middle;">🖨️</span>
</button>
    <button id="printButton">
        Export to 
        <img src="../assets/pdf.png" alt="PDF Icon">
    </button>
    <button onclick="exportToExcel()">
        Export to 
        <img src="../assets/excel.png" alt="Excel Icon">
    </button>
    <button onclick="window.location.href='../preview/preview.php'">
   Back
   <span style="font-size: 45px; display: inline-block; vertical-align: middle;">↩️</span>
</button>
</div>

    </div>

    <script>

function exportToExcel() {
    const table = document.querySelector('table');
    
    // Ambil informasi dari header
    const productName = document.querySelector('.report-title p:nth-child(2)').textContent.split(': ')[1].trim();
    const lotNumber = document.querySelector('.report-title p:nth-child(3)').textContent.split(': ')[1].trim();
    const printDate = document.querySelector('.report-title p:nth-child(4)').textContent.split(': ')[1].trim().replace(/:/g, '-');
    
    // Format nama file
    const fileName = `Product Report - ${productName} - No. Lot ${lotNumber} - ${printDate}.xlsx`;
    
    // Buat workbook dan worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(table, {
        cellDates: true,
        dateNF: 'yyyy-mm-dd hh:mm:ss'
    });
    
    // Persiapkan header informasi
    const headerRows = [
        ['Meiji Product Report'],
        [`Product: ${productName}`],
        [`Lot Number: ${lotNumber}`],
        [`Print Date: ${printDate}`],
        [] // Empty row for spacing
    ];
    
    // Convert worksheet data ke JSON array
    const data = XLSX.utils.sheet_to_json(ws, {header: 1, cellDates: true});
    
    // Gabungkan header dengan data tabel
    const finalWs = XLSX.utils.aoa_to_sheet(headerRows.concat(data));
    
    // Dapatkan range tabel untuk formatting
    const tableRange = XLSX.utils.decode_range(finalWs['!ref']);
    const tableStart = headerRows.length;
    
    // Tambahkan properti table untuk mengaktifkan fitur filter
    finalWs['!autofilter'] = {
        ref: XLSX.utils.encode_range({
            s: {r: tableStart, c: 0},
            e: {r: tableRange.e.r, c: tableRange.e.c}
        })
    };
    
    // Style definitions - menggunakan warna default Excel Table Style Medium 9
    const headerStyle = {
        fill: {
            type: 'pattern',
            pattern: 'solid',
            fgColor: {rgb: "5B9BD5"} // Biru Excel default
        },
        font: {
            name: 'Calibri',
            color: {rgb: "FFFFFF"},
            bold: true,
            sz: 11
        },
        border: {
            top: {style: 'thin', color: {rgb: "5B9BD5"}},
            bottom: {style: 'thin', color: {rgb: "5B9BD5"}},
            left: {style: 'thin', color: {rgb: "5B9BD5"}},
            right: {style: 'thin', color: {rgb: "5B9BD5"}}
        },
        alignment: {
            horizontal: 'center',
            vertical: 'center'
        }
    };
    
    const dataStyle = {
        font: {
            name: 'Calibri',
            sz: 11
        },
        border: {
            top: {style: 'thin', color: {rgb: "D3D3D3"}},
            bottom: {style: 'thin', color: {rgb: "D3D3D3"}},
            left: {style: 'thin', color: {rgb: "D3D3D3"}},
            right: {style: 'thin', color: {rgb: "D3D3D3"}}
        }
    };
    
    const alternateRowStyle = {
        fill: {
            type: 'pattern',
            pattern: 'solid',
            fgColor: {rgb: "DEEBF7"} // Biru muda Excel default
        },
        font: {
            name: 'Calibri',
            sz: 11
        },
        border: {
            top: {style: 'thin', color: {rgb: "D3D3D3"}},
            bottom: {style: 'thin', color: {rgb: "D3D3D3"}},
            left: {style: 'thin', color: {rgb: "D3D3D3"}},
            right: {style: 'thin', color: {rgb: "D3D3D3"}}
        }
    };
    
    // Format setiap sel dalam tabel
    for (let R = tableStart; R <= tableRange.e.r; ++R) {
        for (let C = 0; C <= tableRange.e.c; ++C) {
            const cellRef = XLSX.utils.encode_cell({r: R, c: C});
            
            // Pastikan sel ada sebelum menambahkan style
            if (!finalWs[cellRef]) {
                finalWs[cellRef] = { v: '', t: 's' };
            }
            
            // Tambahkan style sesuai posisi
            if (R === tableStart) {
                finalWs[cellRef].s = headerStyle;
            } else {
                finalWs[cellRef].s = (R - tableStart) % 2 === 0 ? dataStyle : alternateRowStyle;
            }
            
            // Format khusus untuk kolom tertentu
            if (C === 1) { // Barcode
                finalWs[cellRef].t = 's';
            } else if (C === 3) { // JamCode
                finalWs[cellRef].t = 's';
            } else if (C === 4) { // DateTime
                if (finalWs[cellRef].v instanceof Date) {
                    finalWs[cellRef].z = 'yyyy-mm-dd hh:mm:ss';
                }
            }
        }
    }
    
    // Atur lebar kolom
    const columnWidths = Array(tableRange.e.c + 1).fill({ wch: 20 });
    finalWs['!cols'] = columnWidths;
    
    // Tambahkan worksheet ke workbook
    XLSX.utils.book_append_sheet(wb, finalWs, 'Product Report');
    
    // Simpan file
    XLSX.writeFile(wb, fileName);
}




function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        unit: 'mm',
        format: 'a4',
        orientation: 'portrait'
    });

    // Extract report details
    const productName = document.querySelector('.report-title p:nth-child(2)').textContent.split(': ')[1].trim();
    const lotNumber = document.querySelector('.report-title p:nth-child(3)').textContent.split(': ')[1].trim();
    const printDate = document.querySelector('.report-title p:nth-child(4)').textContent.split(': ')[1].trim();
    const fileName = `Product Report - ${productName} - No. Lot ${lotNumber} - ${printDate}.pdf`;

    // Set font
    doc.setFont('times', 'normal');

    // Add logo with updated dimensions
    doc.addImage('../assets/meijiUNMASK.png', 'PNG', 15, 10, 100/3.78, 60/3.78);

    // Add title and details - aligned to far right
    doc.setFontSize(18);
    doc.text('PRODUCT REPORT', 195, 20, { align: 'right' });
    
    doc.setFontSize(12);
    doc.text(`Product: ${productName}`, 195, 30, { align: 'right' });
    doc.text(`Lot Number: ${lotNumber}`, 195, 37, { align: 'right' });
    doc.text(`Print Date: ${printDate}`, 195, 44, { align: 'right' });

    // Add horizontal line
    doc.setLineWidth(0.5);
    doc.line(15, 50, 195, 50);

    // Table configuration
    const columns = ['No', 'RSS-CODE', 'Product', 'Jam Code', 'Date', 'Counter', 'NO. LOT'];
    const tableData = Array.from(document.querySelectorAll('.report-details tbody tr')).map((row, index) => {
        return [
            index + 1,
            row.cells[1].textContent,
            row.cells[2].textContent,
            row.cells[3].textContent,
            row.cells[4].textContent,
            row.cells[5].textContent,
            row.cells[6].textContent
        ];
    });

    // Generate table
    doc.autoTable({
        startY: 60,
        head: [columns],
        body: tableData,
        theme: 'plain',
        styles: {
            font: 'times',
            fontSize: 10,
            cellPadding: 3,
            lineWidth: 0.5,
            lineColor: [0, 0, 0]
        },
        headStyles: {
            fillColor: [245, 245, 245],
            textColor: [0, 0, 0],
            fontStyle: 'bold'
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245]
        }
    });

// Add horizontal line after table
doc.setLineWidth(0.5);
doc.line(15, doc.autoTable.previous.finalY + 12, 195, doc.autoTable.previous.finalY + 12);

// Add summary - aligned to far right
const totalRecords = document.querySelector('.report-summary p').textContent;
doc.setFontSize(12);
doc.text(totalRecords, 195, doc.autoTable.previous.finalY + 20, { align: 'right' });

    // Save PDF
    doc.save(fileName);
}

// Replace existing print button event listener
document.querySelector('#printButton').addEventListener('click', exportToPDF);



    </script>
</body>
</html>
