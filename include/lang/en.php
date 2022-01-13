<?php 
$this->errorMsg =  array();

// ERROR MSG

$this->systemErrorMsg[404] = 'Page not found.';

// DB CONNECTION 
$this->errorMsg[100] = 'Connection failed.';
 
// UDPATE DATA ERROR
$this->errorMsg[200] = 'Item is not valid.';
$this->errorMsg[201] = 'Change of status failed.';
$this->errorMsg[202] = 'Data could not be changed into WAITING status.';
$this->errorMsg[203] = 'Data is not in WAITING status.';
$this->errorMsg[204] = 'Data is not in CONFIRMATION status.';
$this->errorMsg[205] = 'Data is not in FINISH status.';
$this->errorMsg[206] = 'Data is not in ACTIVE status.'; 
$this->errorMsg[207] = 'Data is not in INACTIVE status.'; 
$this->errorMsg[210] = 'Data cannot be deleted.';
$this->errorMsg[211] = 'Predefined data cannot be deleted.'; 
$this->errorMsg[212] = 'Update data failed.';
$this->errorMsg[213] = 'Data not found.';
$this->errorMsg[214] = 'The data has been changed by another user. Please re-edit the data.'; 
$this->errorMsg[215] = 'Duplicate data.'; 
$this->errorMsg[216] = 'File not found.';
$this->errorMsg[217] = 'File not found / some files are still uploading, please wait.'; 
$this->errorMsg[218] = 'Duplication failed.'; 
$this->errorMsg[219] = 'Duplikasi data hanya dapat dilakukan untuk data yang berstatus BATAL.'; 

$this->errorMsg[220] = 'Data sudah berstatus SELESAI.'; 
$this->errorMsg[221] = 'Data sudah berstatus BATAL.'; 
$this->errorMsg[223] = 'Data tidak sesuai.'; 
$this->errorMsg[224] = 'Data tidak dapat diubah ke status yang sama.'; 
$this->errorMsg[225] = 'Data sudah berstatus KONFIRMASI atau SELESAI.';  

$this->errorMsg[250] = 'Anda tidak memiliki hak akses.';
$this->errorMsg[251] = 'Anda tidak memiliki hak akses untuk menghapus data.';
$this->errorMsg[252] = 'Anda tidak memiliki hak akses untuk merubah status transaksi.';


$this->errorMsg[280] = 'Duplicate data.';  

// MEMBER LOGIN, LOGOUT, PROFILE, ACTIVATION etc 
$this->errorMsg[300] = 'Log in failed. Login ID and password did not match.';
$this->errorMsg[301] = '';
$this->errorMsg[302] = 'Account was not found.';
$this->errorMsg[303] = 'Link is expired.';
$this->errorMsg[304] = '';
$this->errorMsg[305] = 'OTP does not match.';

//STOCK
$this->errorMsg[400] = 'This warehouse has been recorded in item movement.';
$this->errorMsg[401] = 'This item has been recorded in item movement.';
$this->errorMsg[402] = 'Stock is insufficient.';
$this->errorMsg[403] = 'This item has been recorded in BOM.';

//TRANSACTION
$this->errorMsg[500] = 'Total transaction and unit price must be bigger than 0.';
$this->errorMsg[501] = 'Transaction details cannot be empty.';
$this->errorMsg[502] = 'Payment is insufficient.';
$this->errorMsg[503] = 'Total transaction must be bigger than 0.'; 
$this->errorMsg[504] = 'GL Error.';
$this->errorMsg[505] = 'Jumlah lebih kecil dari yang telah difaktur.';  
$this->errorMsg[506] = 'Transaksi belum difaktur penuh.'; 
$this->errorMsg[507] = 'Transaksi telah difaktur penuh.'; 
$this->errorMsg[508] = 'Jumlah transaksi melebihi sisa transaksi yang belum difaktur.';  
$this->errorMsg[509] = 'Nilai pembayaran melebihi nilai transaksi.';
$this->errorMsg[510] = 'Jumlah transaksi harus lebih besar dari 0.';
$this->errorMsg[511] = 'Harga unit harus lebih besar dari 0.';
$this->errorMsg[512] = 'Jumlah transaksi tidak boleh sama dengan 0.';

//API 
$this->errorMsg[601] = 'Invalid token.'; 
$this->errorMsg[602] = 'Invalid file.'; 
$this->errorMsg[603] = 'Value cannot be empty.'; 
$this->errorMsg[604] = 'Incorrect data type.'; 

// ETC
$this->errorMsg[900] = 'Change of status failed due to active connection with another data.';
$this->errorMsg[901] = 'Mail delivery failed.'; 
$this->errorMsg[902] = 'Tablekey not found.'; 
$this->errorMsg[903] = 'Cancel reason cannot be empty.'; 
$this->errorMsg[904] = 'Duplication failed due to active connection with another data.';
$this->errorMsg[905] = 'Warehouse did not match.';
$this->errorMsg[906] = 'Value or transaction details have changed.';
  

// EMPTY FIELD
//general
$this->errorMsg['username'][1] = 'Username cannot be empty.';
$this->errorMsg['username'][2] = 'The username already exists. Please try another username.';
$this->errorMsg['username'][3] = 'Username must be between 5 to 30 characters long.';
$this->errorMsg['username'][4] = 'Username can only contain letters, numbers, and underscore.';
$this->errorMsg['username'][5] = 'Username and password did not match.';

$this->errorMsg['code'][1] = 'Code cannot be empty.';
$this->errorMsg['code'][2] = 'Code already exists. Please choose another code.';
$this->errorMsg['code'][3] = 'Code not found.';

$this->errorMsg['name'][1] = 'Name cannot be empty.';
$this->errorMsg['name'][2] = 'Name already exists. Please choose another name.';


// particular field
$this->errorMsg['address'][1] = 'Address cannot be empty.';
$this->errorMsg['zipcode'][1] = 'Zipcode cannot be empty.';

$this->errorMsg['agent'][1] = 'Agent name cannot be empty.'; 
 
$this->errorMsg['assets'][1] = 'Asset name cannot be empty.'; 
$this->errorMsg['assets'][2] = 'Life cycle must be bigger than 0.'; 
$this->errorMsg['assets'][3] = 'COA link cannot be empty.';
$this->errorMsg['assets'][4] = 'Irrevocable assets due to depreciation.';
$this->errorMsg['assets'][5] = 'Assets cannot be depreciated because it has ben been 0 or below the residual value.';

$this->errorMsg['amount'][1] = 'Amount cannot be empty.';
$this->errorMsg['amount'][2] = 'Amount must be bigger than 0.';  

$this->errorMsg['answer'][1] = 'Answer cannot be empty.';

$this->errorMsg['ap'][1] = 'AP cannot be empty.';
$this->errorMsg['ap'][2] = 'AP is not in OPEN status.';
$this->errorMsg['ap'][3] = 'Hutang akan berubah menjadi PARTIAL secara otomatis jika terjadi pembayaran hutang.';
$this->errorMsg['ap'][4] = 'Hutang tidak dapat dibatalkan karena terhubung dengan transaksi pembelian. Hutang ini akan otomatis dibatalkan jika pembelian dibatalkan.';
$this->errorMsg['ap'][5] = 'AP does not belongs to supplier.'; 
$this->errorMsg['ap'][6] = 'Hutang tidak ditemukan atau telah lunas.';
$this->errorMsg['ap'][7] = 'Referensi Hutang berbeda.';
 
$this->errorMsg['apCommission'][2] = 'AP Commission is not in OPEN status.';
    
$this->errorMsg['apPayment'][2] = 'AP payment amount not valid.';
$this->errorMsg['apPayment'][3] = 'AP must be bigger than 0.';
$this->errorMsg['apPayment'][4] = 'AP date must be earlier than payment date.';
$this->errorMsg['apPayment'][5] = 'Invalid currency.';
 
$this->errorMsg['apTax23'][6] = 'Hutang PPH 23 tidak ditemukan atau telah lunas.';

$this->errorMsg['ar'][1] = 'AR cannot be empty.';
$this->errorMsg['ar'][2] = 'AR is not in OPEN status.';
$this->errorMsg['ar'][3] = 'Receivables can only be converted into PARTIAL through the payment of receivables.';
$this->errorMsg['ar'][4] = 'Piutang tidak dapat dibatalkan karena terhubung dengan transaksi penjualan. Piutang ini akan otomatis dibatalkan jika penjualan dibatalkan.'; 
$this->errorMsg['ar'][5] = 'AR does not belongs to customer.'; 
$this->errorMsg['ar'][6] = 'Piutang tidak ditemukan atau telah lunas.';
$this->errorMsg['ar'][7] = 'Piutang tidak dapat dibatalkan karena terhubung dengan transaksi realisasi. Piutang ini akan otomatis dibatalkan jika realisasi dibatalkan.'; 
$this->errorMsg['ar'][8] = 'Piutang tidak mencukupi.';
 
$this->errorMsg['arap'][1] = 'AR / AP netting not balance.';

$this->errorMsg['arEmployee'][2] = 'Employee AR is not in OPEN status.';
$this->errorMsg['arEmployee'][3] = 'Employee AR must be bigger than 0.';

$this->errorMsg['apEmployee'][2] = 'Employee AP is not in OPEN status.';
$this->errorMsg['apEmployee'][3] = 'Employee AP must be bigger than 0.';

$this->errorMsg['arPayment'][2] = 'AR payment amount not valid.';
$this->errorMsg['arPayment'][3] = 'AR must be bigger than 0.';
$this->errorMsg['arPayment'][4] = 'AR date must be earlier than payment date.';
$this->errorMsg['arPayment'][5] = 'Invalid currency.';
$this->errorMsg['arPayment'][6] = 'Pembayaran piutang memerlukan persetujuan tambahan.';

$this->errorMsg['arTax23'][6] = 'PPH 23 dibayar dimuka tidak ditemukan atau telah lunas.';

$this->errorMsg['article'][1] = 'The title of article cannot be empty.';
$this->errorMsg['article'][2] = 'The title of article already exists. Please choose another title.';

$this->errorMsg['assembly'][1] = 'Data BOM berbeda dengan barang yang dihasilkan.'; ; 

$this->errorMsg['banner'][1] = 'Banner name cannot be empty.';
$this->errorMsg['banner'][2] = 'The banner name already exists. Please choose another banner name.';
 
$this->errorMsg['bank'][1] = 'Bank name cannot be empty.';

$this->errorMsg['bankaccountname'][1] = 'Account name cannot be empty.';

$this->errorMsg['bankaccountnumber'][1] = 'Account number cannot be empty.';

$this->errorMsg['billofmaterials'][1] = 'BOM name cannot be empty.';

$this->errorMsg['cashAdvance'][1] = 'Cash advance cannot be empty.';

$this->errorMsg['cashAdvanceRealization'][2] = 'Cash advance not in waiting status.';
$this->errorMsg['cashAdvanceRealization'][3] = 'Invalid balance for cash advance.';
$this->errorMsg['cashAdvanceRealization'][4] = 'Realization for the same cash advance already exists.';
$this->errorMsg['cashAdvanceRealization'][5] = 'Settlement account cannot be empty.';
$this->errorMsg['cashAdvanceRealization'][6] = 'Transaksi detail belum selesai diproses semua.';
$this->errorMsg['cashAdvanceRealization'][7] = 'Kasbon tidak berasal dari gudang yang sama.';

$this->errorMsg['cashBankRealization'][2] = 'Transaksi telah pernah direalisasi.';

$this->errorMsg['company'][1] = 'Company name cannot be empty.';
$this->errorMsg['company'][2] = 'The company name already exists. Please choose another company name.';
$this->errorMsg['company'][3] = 'Company did not match.';

$this->errorMsg['brand'][1] = 'Brand name cannot be empty.';
$this->errorMsg['brand'][2] = 'The brand name already exists. Please choose another brand name.';
  
$this->errorMsg['businessPartner'][1] = 'Business partners\' name cannot be empty.';
 
$this->errorMsg['cancelReason'][1] = 'Cancel reason cannot be empty.';
$this->errorMsg['campaign'][1] = 'Campaign name cannot be empty.';
$this->errorMsg['campaign'][2] = 'Campaign name already exists. Please choose another campaign name.';
$this->errorMsg['campaign'][3] = 'Marketplace detail cannot be empty.';
$this->errorMsg['campaign'][4] = 'Brand detail cannot be empty.';
$this->errorMsg['campaign'][5] = 'Item detail cannot be empty.';
$this->errorMsg['campaign'][6] = 'Item category detail cannot be empty.';
 
$this->errorMsg['car'][1] = 'Registration number cannot be empty.'; 
$this->errorMsg['car'][2] = 'Registration number already exists. Please choose another registration number.';  
$this->errorMsg['car'][3] = 'License number already exists. Please choose another license number.'; 
$this->errorMsg['car'][4] = 'KIR number already exists. Please choose another KIR number.'; 
$this->errorMsg['car'][5] = 'Machine number already exists. Please choose another machine number.'; 
$this->errorMsg['car'][6] = 'Chassis number already exists. Please choose another chassis number.'; 
$this->errorMsg['car'][7] = 'BPKB number already exists. Please choose another BPKB number.'; 
$this->errorMsg['car'][8] = 'Km tidak boleh lebih kecil dari sebelumnya.'; 
$this->errorMsg['car'][9] = 'Registration number not registered. Please choose another registration number.';  

$this->errorMsg['careerField'][1] = 'Career field name cannot be empty..';
$this->errorMsg['careerField'][2] = 'Career field name already exists. Please choose another career field name.';

$this->errorMsg['carServiceMaintenance'][2] = 'Harga dasar telah berubah. Silahkan membuka dan menyimpan ulang data Anda.'; 
 
$this->errorMsg['cashBank'][2] = 'Cash / bank voucher has been used.'; 
$this->errorMsg['cashBank'][3] = 'Cash / bank voucher does not belongs to customer.'; 
$this->errorMsg['cashBank'][4] = 'Insufficient cash / bank voucher.';
$this->errorMsg['cashBank'][5] = 'Voucher kas / bank tidak ditemukan atau telah digunakan.';

$this->errorMsg['chassis'][1] = 'Chassis number cannot be empty.'; 
$this->errorMsg['chassis'][2] = 'Chassis number already exists. Please choose another chassis number.';  
$this->errorMsg['chassis'][3] = 'KIR number already exists. Please choose another KIR number.';

$this->errorMsg['cart'][1] = 'Your cart is empty.'; 

$this->errorMsg['category'][1] = 'Category cannot be empty.';
$this->errorMsg['category'][2] = 'The category name already exists. Please choose another category.';  

$this->errorMsg['captcha'][1] = 'invalid CAPTCHA.'; 

$this->errorMsg['checkIn'][1] = 'Check In failed. Login ID and password did not match.'; 
 
$this->errorMsg['coa'][1] = 'Account cannot be empty.';
$this->errorMsg['coa'][2] = 'The account name already exists. Please choose another account.'; 
$this->errorMsg['coa'][3] = 'invalid account link.';
$this->errorMsg['coa'][4] = 'COA has been used in transaction.'; 
$this->errorMsg['coa'][5] = 'COA did not match.';

$this->errorMsg['coatransfer'][1] = 'The original account and the destination account cannot be the same.';

$this->errorMsg['codriver'][1] = 'Co-driver cannot be empty.';

$this->errorMsg['container'][1] = 'Container type cannot be empty.'; 
$this->errorMsg['container'][2] = 'Container type already exists. Please choose another type.';  
$this->errorMsg['container'][3] = 'Container type did not match.';


$this->errorMsg['cost'][1] = 'Cost cannot be empty.';  
$this->errorMsg['cost'][2] = 'Cost already exists. Please choose another cost.';  

$this->errorMsg['costRate'][1] = 'Cost rate cannot be empty.';  
$this->errorMsg['costRate'][2] = 'Rate with the same information already exists.'; 

$this->errorMsg['currency'][1] = 'Currency cannot be empty.';
$this->errorMsg['currency'][2] = 'The currency name already exists. Please choose another currency.';

$this->errorMsg['customer'][1] = 'The customer name cannot be empty.';
$this->errorMsg['customer'][2] = 'The customer name already exists. Please choose another customer name.'; 
$this->errorMsg['customer'][3] = 'Pelanggan tidak sama.';

$this->errorMsg['city'][1] = 'City cannot be empty.';
$this->errorMsg['city'][2] = 'The city name already exists. Please choose another city name.';
$this->errorMsg['city'][3] = 'City is not valid.';

$this->errorMsg['codeVariant'][1] = 'Variation name / alternative name cannt be empty.';
$this->errorMsg['codeVariant'][2] = 'Variation name for the same group already exists. Please choose another variation name.';
  
$this->errorMsg['creditlimit'][1] = 'Insufficient credit limit.';

$this->errorMsg['creditNote'][1] = 'Credit note cannot be empty.';
$this->errorMsg['creditNote'][2] = 'Credit note amount must be less than invoice amount.';
$this->errorMsg['creditNote'][3] = 'Credit note is not in OPEN status.';
$this->errorMsg['creditNote'][4] = 'Invalid currency.';

$this->errorMsg['chassis'][3] = 'KIR number already exists. Please choose another KIR number.';

$this->errorMsg['date'][1] = 'Date cannot be empty.';
$this->errorMsg['date'][2] = 'Date already exists. Please choose another date.';
$this->errorMsg['date'][3] = 'Starting date must be earlier than end date.';
   
$this->errorMsg['depot'][1] = 'Depot name cannot be empty.';
$this->errorMsg['depot'][2] = 'Depot name already exists. Please choose another name.';
$this->errorMsg['depot'][3] = 'DO code cannot be empty.';
$this->errorMsg['depot'][4] = 'Delivery code cannot be empty.';

$this->errorMsg['division'][1] = 'Division name cannot be empty.';
$this->errorMsg['division'][2] = 'The division name already exists. Please choose another division name.';

$this->errorMsg['download'][1] = 'Title cannot be empty.';
$this->errorMsg['download'][2] = 'Title already exists. Please choose another title.';

$this->errorMsg['downpayment'][3] = 'Sales order does not belongs to customer.'; 
$this->errorMsg['downpayment'][4] = 'Purchase order does not belongs to supplier.';
$this->errorMsg['downpayment'][5] = 'Uang muka telah digunakan.'; 
$this->errorMsg['downpayment'][6] = 'Uang muka tidak sesuai dengan pelanggan.';
$this->errorMsg['downpayment'][7] = 'Uang muka tidak sesuai dengan pemasok.';
$this->errorMsg['downpayment'][8] = 'Nilai melebihi sisa uang muka.'; 
$this->errorMsg['downpayment'][9] = 'Uang muka tidak ditemukan atau telah digunakan.';
$this->errorMsg['downpayment'][10] = 'Invalid currency.';

$this->errorMsg['driver'][1] = 'Driver cannot be empty.';

$this->errorMsg['duedays'][1] = 'Due date cannot be empty.';
$this->errorMsg['duedays'][2] = 'Due date must be bigger than 0.';

$this->errorMsg['email'][1] = 'Email address cannot be empty.';
$this->errorMsg['email'][2] = 'Email address already exists. Please choose another email address.';
$this->errorMsg['email'][3] = 'Email address is not valid.';
$this->errorMsg['email'][4] = 'The email does not exist, please try another email.';
  
$this->errorMsg['emklJobOrder'][2] = 'Detail yang telah difaktur tidak dapat dihapus.';

$this->errorMsg['emklOrderInvoice'][2] = 'Job Order tidak dalam status konfirmasi.'; 
$this->errorMsg['emklOrderInvoice'][3] = 'Job Order tidak sesuai dengan data pelanggan.';
$this->errorMsg['emklOrderInvoice'][4] = 'No Faktur tidak sesuai dengan data pelanggan.';
$this->errorMsg['emklOrderInvoice'][5] = 'No Faktur tidak dalam status konfirmasi.'; 
$this->errorMsg['emklOrderInvoice'][6] = 'Faktur terhubung dengan faktur lain.';  


$this->errorMsg['employee'][1] = 'Staff name cannot be empty.';
$this->errorMsg['employee'][2] = 'The staff name already exists. Please choose another staff name.';
$this->errorMsg['employee'][3] = 'Planner cannot be empty.';

$this->errorMsg['eta'][1] = 'ETA cannot be empty.';  

$this->errorMsg['event'][1] = 'Title of event cannot be empty.';
$this->errorMsg['event'][2] = 'The title of event already exists. Please choose another title of event.';
 
$this->errorMsg['generalJournal'][1] = 'The total of the debit amounts must be equal to the total of the credit amounts.';
$this->errorMsg['generalJournal'][2] = 'Transaction date is greater than today\'s date';
$this->errorMsg['generalJournal'][3] = 'Data is not within current period.';
$this->errorMsg['generalJournal'][4] = 'Delete / change status failed due to active connection with another data.';
$this->errorMsg['generalJournal'][5] = 'Data has been closed.';
$this->errorMsg['generalJournal'][6] = 'transaction is in closed period.';

$this->errorMsg['gramasi'][1] = 'Weight cannot be empty.';
$this->errorMsg['gramasi'][2] = 'Weight must be bigger or the same as 0.';

$this->errorMsg['invoice'][1] = 'Invoice cannot be empty.'; 
$this->errorMsg['invoice'][2] = 'Invoice does not belongs to customer.'; 
$this->errorMsg['invoice'][3] = 'Invoice already has tax invoice.'; 
$this->errorMsg['invoice'][4] = 'Invoice already existed.'; 
$this->errorMsg['invoice'][5] = 'Invalid invoice reference.'; 
 
$this->errorMsg['invoiceTaxNumber'][1] = 'Invoice tax number cannot be empty.';  

$this->errorMsg['item'][1] = 'Item name cannot be empty.';
$this->errorMsg['item'][2] = 'The item name already exists. Please choose another item name.';
$this->errorMsg['item'][3] = 'Jenis barang tidak sama. Silahkan memilih barang lain.'; 
$this->errorMsg['item'][4] = 'Item must be in its base unit.'; 
$this->errorMsg['item'][5] = 'Item short description cannot be empty.'; 
$this->errorMsg['item'][6] = 'Invalid variant item.'; 
//$this->errorMsg['item'][7] = 'Barang bukan parent.'; 

$this->errorMsg['itemAdjustment'][1] = 'Item qty has been changed.'; 

$this->errorMsg['itemCheckList'][2] = 'Qty must be bigger than 0.';
    
$this->errorMsg['itemChecklistGroup'][1] = 'Group name cannot be empty.';

$this->errorMsg['itemFilter'][1] = 'Item filter name cannot be empty.';

$this->errorMsg['itemUnit'][1] = 'Unit name cannot be empty.';
$this->errorMsg['itemUnit'][2] = 'The unit name already exists. Please choose another unit name.';

$this->errorMsg['itemUnitConversion'][1] = 'Unit conversion cannot be empty.';  
$this->errorMsg['itemUnitConversion'][2] = 'You enter the same conversions more than one.';   
$this->errorMsg['itemUnitConversion'][3] = 'This item doesnt have a conversion for the selected unit.';
$this->errorMsg['itemUnitConversion'][4] = 'Transaction unit must have a conversion.';
$this->errorMsg['itemUnitConversion'][5] = 'Conversion must be bigger than 0.';
    
$this->errorMsg['itemParent'][1] = 'Item parent cannot be empty.'; 
$this->errorMsg['itemParent'][2] = 'Item <em>parent</em> tidak boleh memiliki QOH.'; 
 
$this->errorMsg['jobOrder'][1] = 'Job order cannot be empty.'; 
$this->errorMsg['jobOrder'][2] = 'Invalid job order status.';    

$this->errorMsg['jobOrderHeader'][1] = 'Job header cannot be empty.';
$this->errorMsg['jobOrderHeader'][2] = 'Invalid job order header status.';

$this->errorMsg['jobOpportunities'][1] = 'Job opportunities title cannot be empty.';  

$this->errorMsg['jobType'][1] = 'Job type cannot be empty.';
$this->errorMsg['jobType'][2] = 'Job type already exists. Please choose another job type.';

$this->errorMsg['leasing'][2] = 'Loan amount must be bigger than 0.'; 
$this->errorMsg['leasing'][3] = 'Term of lease amount cannot be empty.'; 
$this->errorMsg['leasing'][4] = 'Installment must be bigger than 0.'; 

$this->errorMsg['limit'][1] = 'You have reached your maximum data limit.';
$this->errorMsg['limit'][2] = 'you have reached your maximum images limit.';
$this->errorMsg['limit'][3] = 'you have reached your maximum files limit.';
$this->errorMsg['limit'][4] = 'Image size is too large.';   
$this->errorMsg['limit'][5] = 'File size is too large.';    
$this->errorMsg['limit'][6] = 'you have reached your maximum storage limit.';  
$this->errorMsg['limit'][7] = 'Image file size is too small.';    

$this->errorMsg['location'][1] = 'Location cannot be empty.';
$this->errorMsg['location'][2] = 'Location name already exists. Please choose another location name.';
$this->errorMsg['location'][3] = 'Location is not valid.';

$this->errorMsg['login'][1] = 'Login failed. Your account is not yet activated.';
$this->errorMsg['login'][2] = 'Login failed. Your account account has been suspended.';  
$this->errorMsg['login'][3] = 'Too many failed login attempts. Please try again in {{LOCKOUT_MINUTES}} minutes.';  
 
$this->errorMsg['marketplace'][1] = 'Marketplace name cannot be empty.';
$this->errorMsg['marketplace'][2] = 'Marketplace name already exists. Please choose another name.'; 
$this->errorMsg['marketplace'][3] = 'Transaction for this marketplace already exists.'; 
$this->errorMsg['marketplace'][4] = 'Product information cannot be empty.';
$this->errorMsg['marketplace'][5] = 'Marketplace categoory cannot be empty.';
    
$this->errorMsg['maxattendance'][2] = 'Anda telah melewati jumlah maksimum kehadiran.';

$this->errorMsg['maxStockQty'][1] = 'Maximum stock cannot be empty.';
$this->errorMsg['maxStockQty'][2] = 'Maximum stock must be bigger or the same as 0.';

$this->errorMsg['media'][1] = 'Media cannot be empty.';

$this->errorMsg['message'][1] = 'Message cannot be empty.';

$this->errorMsg['minStockQty'][1] = 'Minimum stock cannot be empty.';
$this->errorMsg['minStockQty'][2] = 'Minimum stock must be bigger or at least equal with 0.'; 

$this->errorMsg['news'][1] = 'News title cannot be empty.';
$this->errorMsg['news'][2] = 'The news title already exists. Please choose another news title.';

$this->errorMsg['occupation'][1] = 'Occupation cannot be empty.';
 
$this->errorMsg['oilType'][1] = 'Oil type name cannot be empty.';
$this->errorMsg['oilType'][2] = 'Oil type name already exists. Please choose another name.';

$this->errorMsg['orderList'][1] = 'Serial number cannot be empty.';
$this->errorMsg['orderList'][2] = 'Serial number must contain numeric.';

$this->errorMsg['page'][1] = 'Page name cannot be empty.';
$this->errorMsg['page'][2] = 'The page name already exists. Please choose another page name.';
 
$this->errorMsg['paymentConfirmation'][1] = 'Sales order not found.'; 
$this->errorMsg['paymentConfirmation'][2] = 'Sales order has been paid.';
    
$this->errorMsg['password'][1] = 'Password cannot be empty.';
$this->errorMsg['password'][2] = 'Password must be between 8 to 30 characters long.';
$this->errorMsg['password'][3] = 'Password and password confirmation did not match.';
$this->errorMsg['password'][4] = 'Password must be between 8 to 30 characters in length and should include at least one upper case letter, one number, and one special character.';

$this->errorMsg['passwordConfirmation'][1] = 'Password confirmation cannot be empty.';
$this->errorMsg['passwordConfirmation'][2] = 'Password confirmation must be between 5 to 30 characters long.';

$this->errorMsg['pawnSalesOrder'][1] = 'Total pinjaman harus lebih kecil atau sama dengan dari nilai barang.';

$this->errorMsg['paymentMethod'][1] = 'Payment method cannot be empty.';
$this->errorMsg['paymentMethod'][2] = 'Payment method already exists. Please choose anther payment method.';

$this->errorMsg['phone'][1] = 'Phone number cannot be empty.';

$this->errorMsg['point'][1] = 'Point cannot be empty.';
$this->errorMsg['point'][2] = 'The total point must be bigger than 0.';
$this->errorMsg['point'][3] = 'Total point is insufficient.';

$this->errorMsg['temporaryAccount'][1] = 'Temporary account cannot be empty.';

$this->errorMsg['terminal'][1] = 'Terminal name cannot be empty.';
$this->errorMsg['terminal'][2] = 'Terminal name already exists. Please choose another name.';
 
$this->errorMsg['ticketSupport'][1] = 'Subject cannot be empty.';
$this->errorMsg['ticketSupport'][2] = 'Message cannot be empty.';

$this->errorMsg['ticketSupportWorkOrder'][1] = 'PIC cannot be empty.';
$this->errorMsg['ticketSupportWorkOrder'][2] = 'Job description cannot be empty.';

$this->errorMsg['port'][1] = 'Port name cannot be empty.';
$this->errorMsg['port'][2] = 'Port name already exists. Please choose another name.';

$this->errorMsg['portfolio'][1] = 'Portfolio name cannot be empty.';
$this->errorMsg['portfolio'][2] = 'Portfolio name already exists. Please choose anther name.';

$this->errorMsg['prepaidExpense'][1] = 'Prepaid expense cannot be empty.';
$this->errorMsg['prepaidExpense'][2] = 'Prepaid expense is not in OPEN status.';
$this->errorMsg['prepaidExpense'][3] = 'Prepaid expense can only be converted into PARTIAL through the reconsiliation.';
$this->errorMsg['prepaidExpense'][4] = 'Biaya Dimuka tidak dapat dibatalkan karena terhubung dengan transaksi pembelian. Biaya Dimuka ini akan otomatis dibatalkan jika pembelian dibatalkan.';
$this->errorMsg['prepaidExpense'][6] = 'Biaya Dimuka tidak ditemukan atau telah lunas.';

$this->errorMsg['project'][1] = 'Project name cannot be empty.'; 

$this->errorMsg['pricelist'][1] = 'Price list name cannot be empty.'; 
$this->errorMsg['pricelist'][2] = 'Price list name already exists. Please choose another name.'; 
$this->errorMsg['pricelist'][3] = 'Price list cannot be empty.'; 

$this->errorMsg['print'][1] = 'You haven\'t choose a data that want to be printed.';
$this->errorMsg['print'][2] = 'Print failed due to invalid status data.'; 
$this->errorMsg['print'][3] = 'All invoice must have the same customer.'; 
$this->errorMsg['print'][4] = 'All invoice must have the same tax.'; 

$this->errorMsg['purchaseOrder'][1] = 'Purchase order cannot be empty.';  
$this->errorMsg['purchaseOrder'][2] = 'Irrevocable purchase order due to receipt of goods. Please cancel the good reception first.';  

$this->errorMsg['purchaseReceive'][1] = 'The value must be bigger than 0 or smaller than the deficiency.';  
$this->errorMsg['purchaseReceive'][2] = 'Date of acceptance should be the same as or later than date of purchase.';  
$this->errorMsg['purchaseReceive'][3] = 'Received qty has been changed. Please reopen and save your data.'; 

$this->errorMsg['purchaseRequest'][1] = 'Purchase request cannot be empty.';  
$this->errorMsg['purchaseRequest'][2] = 'Purchase request not in CONFIRMED or CLOSED state.';

$this->errorMsg['qty'][1] = 'Qty must be bigger than 0.';  
 
$this->errorMsg['questionnaire'][2] = 'All questions need to be answered.';

$this->errorMsg['question'][1] = 'Question cannot be empty.';

$this->errorMsg['salesOrderProperty'][1] = 'Transaction amount name cannot be empty.'; 
$this->errorMsg['salesOrderProperty'][2] = 'Seller name cannot be empty.';
$this->errorMsg['salesOrderProperty'][3] = 'Buyer name cannot be empty.';
$this->errorMsg['salesOrderProperty'][4] = 'Bank name cannot be empty.';

$this->errorMsg['rate'][1] = 'Rate name cannot be empty.'; 
$this->errorMsg['rate'][2] = 'Rate name already exists.'; 
$this->errorMsg['rate'][3] = 'Rate does not belongs to customer.';
$this->errorMsg['rate'][4] = 'Rate has been changed.';
$this->errorMsg['rate'][5] = 'Rate not valid.'; ; 

$this->errorMsg['rating'][1] = 'Rating must between 1 to 5.'; 

$this->errorMsg['realization'][1] = 'Nilai realisasi harus lebih kecil atau sama dengan nilai yang dikeluarkan.'; 

$this->errorMsg['recipient'][1] = 'Recipient cannot be empty.'; 
$this->errorMsg['recipient'][2] = 'Recipient did not match.'; 

$this->errorMsg['reference'][1] = 'Reference cannot be empty.'; 

$this->errorMsg['registration'][1] = 'Anda harus setuju dengan syarat dan ketentuan.'; 

$this->errorMsg['represented'][1] = 'Represented cannot be empty.';

$this->errorMsg['review'][1] = 'Review cannot be empty.';  

$this->errorMsg['salesOrder'][1] = 'Sales order cannot be empty.';  
$this->errorMsg['salesOrder'][2] = 'Sales order cannot be cancel because there has been a shipment of goods. Please cancel the goods shipment first.';  
$this->errorMsg['salesOrder'][3] = 'Transaksi telah diretur.';

$this->errorMsg['salesDelivery'][1] = 'The amount must be bigger than 0 and smaller than the deficiency.';  
$this->errorMsg['salesDelivery'][2] = 'Date of acceptance should be the same as or later than date of sales.';  
$this->errorMsg['salesDelivery'][3] = 'Received qty has been changed. Please reopen and save your data.'; 

$this->errorMsg['salesOrderRental'][1] = 'Sales order cannot be empty.';  
$this->errorMsg['salesOrderRental'][2] = 'Sales quotation does not belongs to customer';  
$this->errorMsg['salesOrderRental'][3] = 'Jml. barang melebihi penawaran.';  

$this->errorMsg['salesRentalQuotation'][1] = 'Sales quotation cannot be empty.';  

$this->errorMsg['sellingPrice'][1] = 'Price cannot be empty.';
$this->errorMsg['sellingPrice'][2] = 'Price must be bigger or same as 0.'; 
$this->errorMsg['sellingPrice'][3] = 'Price must be bigger than 0.'; 

$this->errorMsg['sellingRate'][1] = 'Selling rate cannot be empty.';  
$this->errorMsg['sellingRate'][2] = 'Selling rate name already exists.'; 
$this->errorMsg['sellingRate'][3] = 'Selling rate does not belongs to customer.';
$this->errorMsg['sellingRate'][4] = 'Selling rate has been changed.';
$this->errorMsg['sellingRate'][5] = 'Selling rate not valid.'; ; 


$this->errorMsg['series'][1] = 'Series cannot be empty.';  
$this->errorMsg['series'][2] = 'Series name already exists. Please choose another name.';
$this->errorMsg['series'][3] = 'Nama seri tidak sesuai dengan merk.'; 

$this->errorMsg['service'][1] = 'Service cannot be empty.';  
$this->errorMsg['service'][2] = 'Service name already exists. Please choose another name.';

$this->errorMsg['serviceOrder'][1] = 'Service order cannot be empty.';  
$this->errorMsg['serviceOrder'][2] = 'Order penjualan jasa tidak dapat dibatalkan karena sudah terjadi SPK. Silahkan membatalkan SPK terlebih dahulu.';  


$this->errorMsg['shipper'][1] = 'The shipper name cannot be empty.';
$this->errorMsg['shipper'][2] = 'The shipper name already exists. Please choose another shipper name.'; 
$this->errorMsg['shipper'][3] = 'Shipper tidak sama.';

$this->errorMsg['slot'][1] = 'Slot cannot be empty.';
$this->errorMsg['slot'][2] = 'Slot must be bigger or same as 0.';  
$this->errorMsg['slot'][3] = 'Slot must be bigger than 0.';  

$this->errorMsg['serialnumber'][1] = 'SN cannot be empty.';  
$this->errorMsg['serialnumber'][2] = 'SN qty did not match.';  
$this->errorMsg['serialnumber'][3] = 'SN already registered. Please choose another SN.';  
$this->errorMsg['serialnumber'][4] = 'SN not registered. Please choose another SN.';
$this->errorMsg['serialnumber'][6] = 'SN dan part number vendor tidak cocok.';

$this->errorMsg['script'][1] = 'Script cannot be empty.';
  
$this->errorMsg['shipment'][1] = 'Shipment cannot be empty.';
$this->errorMsg['shipment'][2] = 'Shipment service already exists. Please choose anther service.'; 
$this->errorMsg['shipment'][3] = 'Shipment service has been used in transaction so cannot be deleted.'; 
$this->errorMsg['shipment'][4] = 'Shipment service not available.';

$this->errorMsg['shipmentTracking'][1] = 'Tracking number cannot be empty.'; 

$this->errorMsg['subject'][1] = 'Subject cannot be empty.';

$this->errorMsg['supplier'][1] = 'Supplier name cannot be empty.';
$this->errorMsg['supplier'][2] = 'Supplier name already exists. Please choose another name.';

$this->errorMsg['consignee'][1] = 'Consignee name cannot be empty.';
$this->errorMsg['consignee'][2] = 'Consignee name already exists. Please choose another name.';

$this->errorMsg['title'][1] = 'Title cannot be empty.';
$this->errorMsg['title'][2] = 'Title already exists. Please choose another title.';

$this->errorMsg['top'][1] = 'Term of payment cannot be empty.';
$this->errorMsg['top'][2] = 'Term of payment already exists. Please choose another name.';

$this->errorMsg['trucking'][1] = 'Trucking vendor cannot be empty.';   

$this->errorMsg['truckingServiceOrder'][3] = 'Biaya telah pernah dikeluarkan.';   
$this->errorMsg['truckingServiceOrder'][4] = 'Jumlah partai yang selesai lebih kecil dari jumlah partai SPK.';   
$this->errorMsg['truckingServiceOrder'][5] = 'Job Order tidak dalam status PROSES SPK atau SPK SELESAI.';  

$this->errorMsg['truckingServiceOrderInvoice'][2] = 'Job Order tidak dalam status SIAP DIFAKTUR.';  
$this->errorMsg['truckingServiceOrderInvoice'][3] = 'Job Order tidak sesuai dengan data pelanggan.';   
$this->errorMsg['truckingServiceOrderInvoice'][4] = 'Faktur terhubung dengan faktur lain.';  
$this->errorMsg['truckingServiceOrderInvoice'][5] = 'Jenis pajak tidak sama dengan faktur sebelumnya.';   
$this->errorMsg['truckingServiceOrderInvoice'][6] = 'Jumlah faktur sebagian berbeda dengan sebelumnya.';     
$this->errorMsg['truckingServiceOrderInvoice'][7] = 'Referensi faktur memiliki pelanggan yang berbeda.';   

$this->errorMsg['truckingServiceWorkOrder'][1] = 'SPK cannot be empty.';   
$this->errorMsg['truckingServiceWorkOrder'][2] = 'SPK hasn\'t finished yet.';   
$this->errorMsg['truckingServiceWorkOrder'][3] = 'Biaya telah pernah dikeluarkan.';   
$this->errorMsg['truckingServiceWorkOrder'][4] = 'Pemasok tidak sesuai dengan order pembelian.';  
$this->errorMsg['truckingServiceWorkOrder'][5] = 'Transaksi tidak dapat diselesaikan karena masih terdapat kas keluar dalam status MENUNGGU.';     
$this->errorMsg['truckingServiceWorkOrder'][6] = 'Penerima hanya boleh salah satu dari karyawan atau pemasok.';    
$this->errorMsg['truckingServiceWorkOrder'][7] = 'Biaya melebihi batas yang ditentukan.';   
$this->errorMsg['truckingServiceWorkOrder'][8] = 'Biaya outsource tidak boleh melebihi uang muka outsource.';
$this->errorMsg['truckingServiceWorkOrder'][9] = 'Detail layanan tidak dapat diubah karena SPK tidak dalah status MENUNGGU.'; 
$this->errorMsg['truckingServiceWorkOrder'][10] = 'Ongkos trucking harus diisi.'; 

$this->errorMsg['url'][1] = 'URL cannot be empty.';
$this->errorMsg['url'][2] = 'URL already exists. Please choose another URL.';
$this->errorMsg['url'][3] = 'URL is not valid.';

$this->errorMsg['vendorPartNumber'][1] = 'Vendor\'s part number cannot be empty.';  
$this->errorMsg['vendorPartNumber'][2] = 'Vendor\'s part number already registered. Please choose another part number.';  
$this->errorMsg['vendorPartNumber'][3] = 'Vendor\'s part number did not match.';

$this->errorMsg['warehouse'][1] = 'Warehouse name cannot be empty.';
$this->errorMsg['warehouse'][2] = 'Warehouse name already exists. Please choose another warehouse name.';
 
$this->errorMsg['warehouseTransfer'][2] = 'Gudang asal dan gudang tujuan tidak boleh sama.';
$this->errorMsg['warehouseTransfer'][3] = 'Jumlah asal dan tujuan tidak sama.';

$this->errorMsg['warrantyClaim'][2] = 'Alasan harus diisi.';
$this->errorMsg['warrantyClaim'][3] = 'The warranty has expired.';
$this->errorMsg['warrantyClaim'][4] = 'Part Number pengganti harus diisi.';
$this->errorMsg['warrantyClaim'][5] = 'Biaya harus lebih besar dari 0.'; 
$this->errorMsg['warrantyClaim'][6] = 'SN pengganti tidak boleh sama dengan SN yang di claim.';
$this->errorMsg['warrantyClaim'][7] = 'Item pengganti harus sama.';
$this->errorMsg['warrantyClaim'][8] = 'SN pengganti tidak boleh kosong.';

$this->errorMsg['youtube'][1] = 'Youtube title cannot be empty.';
$this->errorMsg['youtube'][2] = 'Youtube title already exists. Please choose another youtube title.';
$this->errorMsg['youtube'][3] = 'Youtube ID cannot be empty.';

$this->errorMsg['warrantyPeriod'][1] = 'Warranty period cannot be empty.';   
$this->errorMsg['warrantyPeriod'][2] = 'Warranty period must be bigger than 0.';  
 
$this->errorMsg['carrier'][1] = 'Carrier name cannot be empty.';
$this->errorMsg['carrier'][2] = 'Carrier already exists. Please choose another carrier.';

$this->errorMsg['vendorWarrantyClaim'][3] = 'Nama barang yang hendak diganti tidak sesuai dengan histori claim.';
$this->errorMsg['vendorWarrantyClaim'][4] = 'SN tidak sesuai.';


$this->errorMsg['vendorWarrantyClaimReturn'][2] = 'SN pengganti harus diisi.';

$this->errorMsg['volume'][1] = 'Volume cannot be empty.';
$this->errorMsg['volume'][2] = 'Volume must be bigger than 0.';

$this->errorMsg['itemIn'][1] = 'Item In code cannot be empty.';  
$this->errorMsg['itemIn'][2] = 'Pemasukan barang telah memiliki penerimaan barang. Silahkan membatalkan penerimaan barang terlebih dahulu.';  

$this->errorMsg['itemOut'][1] = 'Item Out code cannot be empty.';  
$this->errorMsg['itemOut'][2] = 'Pengeluaran barang telah memiliki pengiriman barang. Silahkan membatalkan pengiriman barang terlebih dahulu.';  

$this->errorMsg['itemInReceive'][2] = 'Tanggal penerimaan barang harus lebih besar atau sama dengan tanggal pemasukan barang.';  

$this->errorMsg['itemOutDelivery'][2] = 'Tanggal pengiriman barang harus lebih besar atau sama dengan tanggal pengeluaran barang.';  
 
$this->errorMsg['issue'][1] = 'Issue Out code cannot be empty.';  
     
$this->errorMsg['membership'][1] = 'Keanggotaan tidak valid.';   

$this->errorMsg['membershipAttendance'][2] = 'Registrasi Keanggotaan telah memiliki Kehadiran Keanggotaan. Silahkan membatalkan Kehadiran Keanggotaan terlebih dahulu.';  

$this->errorMsg['customerMembership'][1] = 'Registrasi Keanggotaan harus diisi.';  
$this->errorMsg['customerMembership'][2] = 'Registrasi Keanggotaan telah memiliki Transaksi Voucher. Silahkan membatalkan Transaksi Voucher terlebih dahulu.';  
$this->errorMsg['customerMembership'][3] = 'Tanggal kehadiran tidak didalam periode keanggotaan.';  
$this->errorMsg['customerMembership'][4] = 'Keanggotaan tidak valid.';   

$this->errorMsg['warrantyClaimProgress'][2] = 'Jenis barang harus sama.';  

$this->errorMsg['RMA'][1] = 'RMA number cannot be empty.';
$this->errorMsg['RMA'][2] = 'RMA number already exists. Please choose another RMA number.';

$this->errorMsg['width'][2] = 'Width must be bigger than 0.'; 
$this->errorMsg['length'][2] = 'Length must be bigger than 0.'; 
$this->errorMsg['height'][2] = 'Height must be bigger than 0.'; 

$this->errorMsg['routineCost'][1] = ''; 
$this->errorMsg['routineCost'][2] = 'Cost description cannot be empty.'; 


$this->errorMsg['voucher'][2] = 'Voucher value must be bigger than 0.'; 
$this->errorMsg['voucher'][3] = 'Voucher status not valid.'; 
$this->errorMsg['voucher'][4] = 'Voucher melebihi kuota yagn disediakan.'; 
$this->errorMsg['voucher'][5] = 'Kepemilikan voucher tidak sesuai.'; 
$this->errorMsg['voucher'][6] = 'Voucher not valid.'; 
$this->errorMsg['voucher'][7] = 'Keanggotaan Anda belum dapat menggunakan voucher.'; 
$this->errorMsg['voucher'][8] = 'Voucher has expired.'; 
 
$this->errorMsg['technician'][1] = 'Technician cannot be empty.';


$this->errorMsg['installationworkorder'][2] = 'Penjualan SC tidak dalam status konfirmasi.';
$this->errorMsg['installationworkorder'][3] = 'Jml terpakai harus lebih kecil dari jml trasansaksi.';


$this->errorMsg['invoicePeriod'][1] = 'Periode harus diisi.';  
$this->errorMsg['invoicePeriod'][2] = 'Periode telah terdaftar. Silahkan memilih Periode lain.';  
$this->errorMsg['invoicePeriod'][3] = 'Periode invoice harus lebih besar dari 0.';

$this->errorMsg['salesOrderSubscription'][1] = 'Penjualan SC harus diisi.';  
$this->errorMsg['salesOrderSubscription'][2] = 'Penjualan SC tidak dalam status konfirmasi.';
$this->errorMsg['salesOrderSubscription'][3] = 'Penjualan SC akan berubah menjadi terminated secara otomatis jika terjadi terminasi.';
$this->errorMsg['salesOrderSubscription'][4] = 'Penjualan SC tidak dalam status selesai.';
$this->errorMsg['salesOrderSubscription'][5] = 'Penjualan SC tidak dalam status selesai atau onhold.';

$this->errorMsg['quiz'][2] = 'All the questions must be answered.';

$this->errorMsg['shortDescription'][2] = 'Short description must be more than 50 characters long.';

$this->errorMsg['membershipLevel'][1] = 'Jenis keanggotaan harus diisi.';
$this->errorMsg['membershipLevel'][2] = 'Jenis keanggotaan sudah terdaftar.';

// WEB CONTENT   
$this->lang['2FAuthentication'] = '2F Authentication'; 
$this->lang['2FDisabled'] = '2F Authentication is Disabled';
$this->lang['2FEnabled'] = '2F Authentication is Enabled';
$this->lang['AC'] = 'AC';  
$this->lang['AH'] = 'AH';  
$this->lang['APAgingReport'] = 'AP Aging Report';     
$this->lang['APPaymentReport'] = 'AP Payment Report' ;
$this->lang['APReport'] = 'AP Report' ;
$this->lang['ARAccount'] = 'AR Account'; 
$this->lang['ARAgingReport'] = 'AR Aging Report';     
$this->lang['ARPaymentReport'] = 'AR Payment Report' ;
$this->lang['ARReport'] = 'AR Report' ;
$this->lang['GL'] = 'GL';
$this->lang['GPSID'] = 'GPS ID';
$this->lang['GPSTracker'] = 'GPS Tracker'; 
$this->lang['IDInformation'] = 'ID Information'; 
$this->lang['IDNumber'] = 'ID Number'; 
$this->lang['JOCode'] = 'Kode JO';   
$this->lang['PEB'] = 'PEB'; 
$this->lang['PPN'] = 'PPN';
$this->lang['RMANumber'] = 'RMA Number';
$this->lang['TL'] = 'TL'; 
$this->lang['WOCode'] = 'WO Code'; 
$this->lang['aboutus'] = 'About Us' ;
$this->lang['acPackage'] = 'AC Package';
$this->lang['account'] = 'Account'; 
$this->lang['accountActivation'] = 'Account Activation' ;
$this->lang['accountActivationSuccessful'] = 'Congratulations, your account has been activated!<br>Now you can start logging in accessing our member features, have fun !' ;
$this->lang['accountCode'] = 'Account Code'; 
$this->lang['accountInformation'] = 'Account Information' ;
$this->lang['accountName'] = 'Account Name'; 
$this->lang['accountRecovery'] = 'Account Recovery' ;
$this->lang['accountsPayable'] = 'Accounts Payable' ;
$this->lang['accountsPayableBalance'] = 'Accounts Payable Balance';
$this->lang['accountsPayablePayment'] = 'Accounts Payable Payment' ; 
$this->lang['accountsPayablePaymentReport'] = 'Accounts Payable Payment Report' ; 
$this->lang['accountsPayableReport'] = 'Accounts Payable Report' ;
$this->lang['accountsReceivable'] = 'Accounts Receivable' ;
$this->lang['accountsReceivablePayment'] = 'Accounts Receivable Payment' ;
$this->lang['accountsReceivablePaymentReport'] = 'Accounts Receivable Payment Report' ;
$this->lang['accountsReceivableReport'] = 'Accounts Receivable Report' ;
$this->lang['accu'] = 'Accu';  
$this->lang['accuCheck'] = 'Accu Check';  
$this->lang['action'] = 'Action' ;
$this->lang['actionTime'] = 'Action Time' ;
$this->lang['activationEmail'] = 'Activation Email' ;
$this->lang['activity'] = 'Activity';  
$this->lang['add'] = 'Add';
$this->lang['addCustomer'] = 'Add Customer' ;
$this->lang['addReimburse'] = 'Add Reimbursement';
$this->lang['addReimburseCost'] = 'Add Reimbursement';
$this->lang['addRows'] = 'Add Rows' ; 
$this->lang['addSearchFilter'] = 'Add Search Filter';
$this->lang['addToCart'] = 'Add to Cart' ;
$this->lang['additional'] = 'Additional'; 
$this->lang['additionalCost'] = 'Additional Cost';
$this->lang['address'] = 'Address' ;
$this->lang['adjustment'] = 'Adjustment'; 
$this->lang['after'] = 'after';  
$this->lang['afterSales'] = 'After Sales'; 
$this->lang['agent'] = 'Agent';
$this->lang['aging'] = 'Aging';    
$this->lang['airFilter'] = 'Air Filter';  
$this->lang['airwayBill'] = 'Airway Bill'; 
$this->lang['alias'] = 'Alias'; 
$this->lang['allCOA'] = 'All COA';
$this->lang['allCategories'] = 'All Categories' ;
$this->lang['allLocation'] = 'All Location';
$this->lang['allProducts'] = 'All Products';
$this->lang['allBrands'] = 'All Brands';
$this->lang['allMarketplace'] = 'All Marketplace';
$this->lang['allWarehouse'] = 'All Warehouses';
$this->lang['amount'] = 'Amount';
$this->lang['ap'] = 'AP'; 
$this->lang['ap/ar'] = 'AP / AR';
$this->lang['apAccount'] = 'AP Account'; 
$this->lang['apCode'] = 'AP Code'; 
$this->lang['ar'] = 'AR';
$this->lang['ar/ap'] = 'AR / AP' ;
$this->lang['arAccount'] = 'AR Account'; 
$this->lang['arCode'] = 'AR Code'; 
$this->lang['article'] = 'Article';
$this->lang['articleCategory'] = 'Article Category'; 
$this->lang['articleContent'] = 'Article Content'; 
$this->lang['articleNewsAndMedia'] = 'Article, News & Media'; 
$this->lang['asCost'] = 'As Cost'; 
$this->lang['assembly'] = 'Assembly' ;
$this->lang['assemblyItem'] = 'Assembly Item'; 
$this->lang['assets'] = 'Assets' ;
$this->lang['assetsCategory'] = 'Assets Category' ;
$this->lang['assetsDepreciation'] = 'Assets Depreciation' ;
$this->lang['assetsList'] = 'Assets List' ;
$this->lang['assetsPurchaseOrder'] = 'Purchase Order (Assets)';
$this->lang['attachment'] = 'Attachment'; 
$this->lang['authenticationCode'] = 'Authentication Code'; 
$this->lang['availability'] = 'Availability';
$this->lang['axis'] = 'Axis';
$this->lang['back'] = 'Back'; 
$this->lang['backTo'] = 'Back to';
$this->lang['backToTop'] = 'Back to Top';
$this->lang['balance'] = 'Balance'; 
$this->lang['balanceQty'] = 'Balance';
$this->lang['balanceSheetReport'] = 'Balance Sheet Report';
$this->lang['bankAccountName'] = 'Account Name';
$this->lang['bankAccountNumber'] = 'Account Number';
$this->lang['bankName'] = 'Bank Name';
$this->lang['banner'] = 'Banner';
$this->lang['baseunit'] = 'Base Unit';
$this->lang['bbmPackage'] = 'BBM Package'; 
$this->lang['before'] = 'before';  
$this->lang['beforeTax'] = 'Before Tax';
$this->lang['beginningCOGS'] = 'COGS Awal'; 
$this->lang['beginningQty'] = 'Jml. Awal'; 
$this->lang['bestSellingItems'] = 'Best Selling Items';
$this->lang['billOfMaterials'] = 'Bill of Materials'; 
$this->lang['billingStatement'] = 'Billing Statement'; 
$this->lang['bookingDate'] = 'Booking Date'; 
$this->lang['bookingNumber'] = 'Booking Number'; 
$this->lang['bpkbRegisteredName'] = 'Registered Name';
$this->lang['bpkbRegisteredNumber'] = 'Registered Number';
$this->lang['branch'] = 'Branch';
$this->lang['branchOrFranchise'] = 'Branch / Franchise'; 
$this->lang['brand'] = 'Brand';
$this->lang['brandList'] = 'Brand List';
$this->lang['bugReport'] = 'Bug Report'; 
$this->lang['businessLocationAccess'] = 'Business Location Access';
$this->lang['businessPartner'] = 'Business Partner';  
$this->lang['buying'] = 'Buying'; 
$this->lang['campaignDate'] = 'Campaign Date';
$this->lang['cancel'] = 'Cancel';
$this->lang['cancelReason'] = 'Cancel Reason';  
$this->lang['cancellationRate'] = 'Cancellation rate';
$this->lang['capacity'] = 'Capacity';  
$this->lang['car'] = 'Car';  
$this->lang['carCategory'] = 'Car Category';
$this->lang['carInformation'] = 'Informasi Mobil'; 
$this->lang['carList'] = 'Car List';
$this->lang['carMaintenance'] = 'Car Maintenance';
$this->lang['carMaintenanceHistoryReport'] = 'Car Maintenance History Report';
$this->lang['carRegistrationNumber'] = 'Registration Number'; 
$this->lang['carReport'] = 'Car Report'; 
$this->lang['carRevenue'] = 'Car Revenue';
$this->lang['carSeries'] = 'Car Series';  
$this->lang['carService'] = 'Car Service';
$this->lang['carTurnoverReport'] = 'Car Turnover Report';   
$this->lang['cargoType'] = 'Cargo Type';  
$this->lang['carrier'] = 'Carrier';
$this->lang['carrierBookingNumber'] = 'Carrier Booking Number'; 
$this->lang['cart'] = 'Cart';
$this->lang['cartSubmitSuccessful'] = 'Your order has been submitted. You will receive an invoice with billing details and payment information in your email soon.';
$this->lang['cash'] = 'Cash';  
$this->lang['cash/ap'] = 'Cash / AP'; 
$this->lang['cashBack'] = 'Cash Back'; 
$this->lang['cashBank'] = 'Cash Bank';
$this->lang['cashBankAccount'] = 'Cash Bank Account'; 
$this->lang['cashBankAccount'] = 'Cash Bank Account';    
$this->lang['cashBankRealization'] = 'Cash Bank Realization';   
$this->lang['cashBankRealizationReport'] = 'Cash Bank Realization Report';    
$this->lang['cashBankTransfer'] = 'Cash Bank Transfer';
$this->lang['cashBankTransferReport'] = 'Cash Bank Transfer Report';
$this->lang['cashFlowReport'] = 'Cash Flow Report';
$this->lang['cashIn'] = 'Cash In';
$this->lang['cashInReport'] = 'Cash In Report';
$this->lang['cashMovementReport'] = 'Cash Movement Report';
$this->lang['cashOut'] = 'Cash Out'; 
$this->lang['cashOutCode'] = 'Kode Kas Keluar'; 
$this->lang['cashOutDate'] = 'Cash Out Date';
$this->lang['cashOutReport'] = 'Cash Out Report';
$this->lang['category'] = 'Category';
$this->lang['change'] = 'change';  
$this->lang['changeStatus'] = 'Change Status';
$this->lang['chartNotAvailable'] = 'Chart Not Available'; 
$this->lang['chartOfAccount'] = 'Chart of Account';
$this->lang['chassis'] = 'Chassis'; 
$this->lang['chassisCategory'] = 'Chassis Category'; 
$this->lang['chassisCategoryList'] = 'Chassis Category List'; 
$this->lang['chassisList'] = 'Chassis List'; 
$this->lang['chassisNumber'] = 'Chassis Number';
$this->lang['check'] = 'Check';
$this->lang['checkOut'] = 'Check Out';
$this->lang['checkingResult'] = 'Checking Result';  
$this->lang['chooseStatus'] = 'Choose Status';
$this->lang['city'] = 'City'; 
$this->lang['cityCategory'] = 'City Category'; 
$this->lang['cityOrLocation'] = 'City / Location'; 
$this->lang['cityOrLocationCategory'] = 'City / Location Category'; 
$this->lang['claim'] = 'Claim';
$this->lang['claimSettlement'] = 'Penyelesaian Klaim';
$this->lang['claimedItem'] = 'Claimed Item';
$this->lang['clean'] = 'clean';  
$this->lang['clearTag'] = 'Clear Tag';
$this->lang['clickHere'] = 'Click Here'; 
$this->lang['close'] = 'Close'; 
$this->lang['closeAll'] = 'Close All'; 
$this->lang['closed'] = 'Closed'; 
$this->lang['closing'] = 'Closing';   
$this->lang['closingDate'] = 'Closing Date'; 
$this->lang['closingPeriod'] = 'Closing Period'; 
$this->lang['coaPrivileges'] = 'COA Privileges';
$this->lang['coalink'] = 'COA Link'; 
$this->lang['code'] = 'Code';
$this->lang['codeSetting'] = 'Code Setting';
$this->lang['codriver'] = 'Co-driver'; 
$this->lang['codriverCommission'] = 'Co-driver commission'; 
$this->lang['cogs'] = 'COGS';
$this->lang['color'] = 'Color';  
$this->lang['commission'] = 'Commission'; 
$this->lang['commissionAP'] = 'AP Commission'; 
$this->lang['commissionAPAccount'] = 'AP Commission'; 
$this->lang['commissionCost'] = 'Commission Cost'; 
$this->lang['commissionPerUnit'] = 'Komisi Satuan';  
$this->lang['commodity'] = 'Commodity';  
$this->lang['company'] = 'Company'; 
$this->lang['companyList'] = 'Company List';  
$this->lang['companyType'] = 'Company Type';
$this->lang['confirm'] = 'Confirm'; 
$this->lang['consignee'] = 'Consignee'; 
$this->lang['consigneeInformation'] = 'Consignee Information';
$this->lang['consigneeReport'] = 'Consignee Report'; 
$this->lang['contact'] = 'Contact';
$this->lang['contactPerson'] = 'Contact Person'; 
$this->lang['contactUs'] = 'Contact Us';
$this->lang['contactUsCategory'] = 'Contact Us Category';
$this->lang['contactUsSuccessful'] = 'Your message has been sent and we will be in touch with you as soon as possible.'; 
$this->lang['container'] = 'Container';
$this->lang['containerNumber'] = 'Container Number'; 
$this->lang['content'] = 'Content';  
$this->lang['contentOfPackage'] = 'Content of Package';
$this->lang['contractDuration'] = 'Contract Duration'; 
$this->lang['contractPrice'] = 'Contract Price'; 
$this->lang['cost'] = 'Cost'; 
$this->lang['expenseAccount'] = 'Exxpense Account';  
$this->lang['costAmount'] = 'Cost Amount'; 
$this->lang['costInformation'] = 'Cost Information'; 
$this->lang['costList'] = 'Cost List'; 
$this->lang['costName'] = 'Cost Name'; 
$this->lang['costRate'] = 'Cost Rate';   
$this->lang['costReport'] = 'Cost Report'; 
$this->lang['costType'] = 'Cost Type';
$this->lang['credit'] = 'Credit'; 
$this->lang['creditLimit'] = 'Credit Limit'; 
$this->lang['curr'] = 'Curr'; 
$this->lang['currency'] = 'Currency'; 
$this->lang['currencyList'] = 'Currency List'; 
$this->lang['currencyRate'] = 'Currency Rate'; 
$this->lang['currencyRate'] = 'Rate';  
$this->lang['currencyShort'] = 'Curr'; 
$this->lang['currentEarning'] = 'Current Earning'; 
$this->lang['currentPassword'] = 'Current Password'; 
$this->lang['currentRate'] = 'Current Rate';
$this->lang['currentworkprogressname'] = 'Current Work Progress';
$this->lang['customCode'] = 'Custom Code';
$this->lang['customer'] = 'Customer'; 
$this->lang['customerCategory'] = 'Customer Category'; 
$this->lang['customerComplain'] = 'Customer Complain';  
$this->lang['customerDO'] = 'Customer DO'; 
$this->lang['customerDownpayment'] = 'Customer Downpayment';
$this->lang['customerDownpaymentReport'] = 'Customer Downpayment Report'; 
$this->lang['customerInformation'] = 'Customer Information'; 
$this->lang['customerInformation'] = 'Customer Information';  
$this->lang['customerPO'] = 'Customer PO'; 
$this->lang['customerReport'] = 'Customer Report'; 
$this->lang['dailyReport'] = 'Daily Report';
$this->lang['dataHasBeenSuccessfullyDeleted'] = 'Data has been successfully deleted.'; 
$this->lang['dataHasBeenSuccessfullyUpdated'] = 'Data has been successfully updated.'; 
$this->lang['date'] = 'Date';
$this->lang['dateAndTime'] = 'Date / Time';
$this->lang['dateOfBirth'] = 'Date of Birth'; 
$this->lang['day'] = 'Day';
$this->lang['day(s)'] = 'day(s)';    
$this->lang['days'] = 'Days'; 
$this->lang['debit'] = 'Debit'; 
$this->lang['default'] = 'Default';
$this->lang['defaultForShipment'] = 'Default Shipment';  
$this->lang['defaultTansactionUnit'] = 'Default Transaction Unit';
$this->lang['delete'] = 'Delete'; 
$this->lang['deliveredQty'] = 'Delivered Qty'; 
$this->lang['deliveryNotes'] = 'Delivery Notes'; 
$this->lang['depot'] = 'Depot'; 
$this->lang['depotItemMovementReport'] = 'Depot\'s Item Movement Report';
$this->lang['depotList'] = 'Depot List';
$this->lang['description'] = 'Description';
$this->lang['deselectAll'] = 'Deselect All';
$this->lang['destination'] = 'Destination'; 
$this->lang['destinationWarehouse'] = 'Destination Warehouse';
$this->lang['detail'] = 'Detail';
$this->lang['digit'] = 'Digit';
$this->lang['disable2fAuthentication'] = 'Disable 2F Authentication'; 
$this->lang['discount'] = 'Discount';
$this->lang['discountScheme'] = 'Discount Scheme';
$this->lang['division'] = 'Division';
$this->lang['doCode'] = 'DO Code';
$this->lang['doDocuments'] = 'Delivery Order Document';
$this->lang['downloadList'] = 'Download List';  
$this->lang['downpayment'] = 'Downpayment'; 
$this->lang['downpaymentAccount'] = 'Downpayment Account'; 
$this->lang['dpp'] = 'DPP';
$this->lang['driver'] = 'Driver';
$this->lang['driverCashBank'] = 'Driver Cash/Bank';
$this->lang['driverCommission'] = 'Driver commission'; 
$this->lang['driverProgressStep'] = 'Driver Progress Step';
$this->lang['driverSummaryReport'] = 'Driver Summary Report';
$this->lang['drivingLicense'] = 'Driving License'; 
$this->lang['drivingLicenseExpirationDate'] = 'Driving License Expired Date';
$this->lang['dropship'] = 'Dropship'; 
$this->lang['dropshiper'] = 'Dropshiper'; 
$this->lang['duedate'] = 'Due Date'; 
$this->lang['duplicate'] = 'Duplicate'; 
$this->lang['duplicateDeletedData'] = 'Duplicate Deleted Data';  
$this->lang['edit'] = 'Edit';
$this->lang['editCustomer'] = 'Edit Customer';
$this->lang['email'] = 'Email';
$this->lang['emailSentSuccessful'] = 'Email has been successfully sent.'; 
$this->lang['emergencyContact'] = 'Emergency Contact'; 
$this->lang['employee'] = 'Employee'; 
$this->lang['employeeAR'] = 'Employee AR'; 
$this->lang['employeeARPayment'] = 'Employee AR Payment'; 
$this->lang['employeeAP'] = 'Employee AP'; 
$this->lang['employeeAPPayment'] = 'Employee AP Payment'; 
$this->lang['employeeARAccount'] = 'Employee AR Account'; 
$this->lang['employeeAccountsReceivable'] = 'Employee AR'; 
$this->lang['employeeAccountsReceivablePayment'] = 'Employee AR Payment';  
$this->lang['employeeAccountsReceivablePaymentReport'] = 'Employee AR Payment Report'; 
$this->lang['employeeAccountsReceivableReport'] = 'Employee AR Report'; 
$this->lang['employeeCommission'] = 'Employee Commission'; 
$this->lang['employeeCommissionPayment'] = 'Employee Commission Payment'; 
$this->lang['employeeCommissionPaymentReport'] = 'Employee Commission Payment Report'; 
$this->lang['employeeCommissionReport'] = 'Employee Commission Report'; 
$this->lang['employeeDivision'] = 'Employee Division'; 
$this->lang['employeeReport'] = 'Employee Report';   
$this->lang['employees'] = 'Employee(s)'; 
$this->lang['emptyFieldPasswordDontChange'] = 'Empty field <strong>New Password</strong> if you do not want to change your password.';
$this->lang['emptyStock'] = 'Empty Stock';
$this->lang['endingBalance'] = 'Ending Balance'; 
$this->lang['eta'] = 'ETA';
$this->lang['etccost'] = 'Cost'; 
$this->lang['etd'] = 'ETD';   
$this->lang['event'] = 'Event';
$this->lang['expirationDate'] = 'Expiration Date'; 
$this->lang['exportExcel'] = 'Export to Excel';
$this->lang['exportOrderSheet'] = 'Export Order Sheet';  
$this->lang['exportTemplate'] = 'Export Template';   
$this->lang['externalWorkshop'] = 'External Workshop'; 
$this->lang['faq'] = 'FAQ';
$this->lang['fax'] = 'Fax';
$this->lang['featuredArticle'] = 'Featured Article';  
$this->lang['featuredItem'] = 'Featured Item'; 
$this->lang['featuredProducts'] = 'Featured Products'; 
$this->lang['file'] = 'File';
$this->lang['fileDiskUsage'] = 'File Disk Usage'; 
$this->lang['fileSize'] = 'File Size'; 
$this->lang['files'] = 'File(s)';
$this->lang['filesPerItem'] = 'File(s) / Item'; 
$this->lang['filter'] = 'Filter';
$this->lang['filterCategory'] = 'Filter Category';
$this->lang['finalDiscount'] = 'Final Discount';
$this->lang['finalPrice'] = 'Harga Akhir';
$this->lang['finance'] = 'Finance';
$this->lang['financialInformation'] = 'Financial Information';
$this->lang['firstPage'] = 'First Page';
$this->lang['fixedCost'] = 'Fixed Cost';
$this->lang['fogging'] = 'fogging';  
$this->lang['followUs'] = 'Follow Us';
$this->lang['for'] = 'for';
$this->lang['forgotPassword'] = 'Forgot Password';
$this->lang['forgotPasswordMessage'] = 'Please enter the email address you used to register with us.';
$this->lang['format'] = 'Format';
$this->lang['freightTerm'] = 'Freight Term';  
$this->lang['frequentlyAskedQuestions'] = 'Frequently Asked Questions'; 
$this->lang['from'] = 'From'; 
$this->lang['fromAccount'] = 'From Account'; 
$this->lang['fullDelivered'] = 'Full Delivered'; 
$this->lang['fullPayment'] = 'Full Payment';
$this->lang['fullReceived'] = 'Full Received';
$this->lang['gallery'] = 'Gallery';
$this->lang['generalInformation'] = 'General Information'; 
$this->lang['generalJournal'] = 'General Journal';
$this->lang['generalJournalReport'] = 'General Journal Report'; 
$this->lang['generalLedger'] = 'General Ledger';
$this->lang['generalLedgerReport'] = 'General Ledger Report'; 
$this->lang['grossProfit'] = 'Gross Profit'; 
$this->lang['group'] = 'Group';
$this->lang['handling'] = 'Handling';
$this->lang['handlingRateFCL'] = 'Handling Rate FCL'; 
$this->lang['hbl'] = 'HBL';
$this->lang['hblNumber'] = 'HBL Number';
$this->lang['height'] = 'Height';
$this->lang['heightShort'] = 'H';
$this->lang['hideDetail'] = 'Hide Detail'; 
$this->lang['hideNotAvailableItem'] = 'Hide Unavailable Item'; 
$this->lang['hidePartial'] = 'Sembunyikan Sebagian';
$this->lang['historyLog'] = 'History Log'; 
$this->lang['home'] = 'Home'; 
$this->lang['hour'] = 'Hour';
$this->lang['hours'] = 'Hour(s)';
$this->lang['image'] = 'Image'; 
$this->lang['imageLink'] = 'Image Link'; 
$this->lang['imageSize'] = 'Image Size'; 
$this->lang['images'] = 'Image(s)'; 
$this->lang['imagesPerItem'] = 'Image(s) / Item'; 
$this->lang['import'] = 'Import'; 
$this->lang['importFrom'] = 'Import From';
$this->lang['importItem'] = 'Import Item';   
$this->lang['importOrderSheet'] = 'Import Order Sheet';  
$this->lang['in'] = 'in';  
$this->lang['inStock'] = 'In Stock';
$this->lang['inThousand'] = 'in Thousand'; 
$this->lang['incomeStatementReport'] = 'Income Statement Report'; 
$this->lang['indexRandomProductTitle'] = 'Our Products'; 
$this->lang['inhouse'] = 'In-House'; 
$this->lang['inhouseCost'] = 'In-House Cost'; 
$this->lang['inhouseCostSummary'] = 'In-House Cost Summary';
$this->lang['insurance'] = 'Insurance';
$this->lang['internalUse'] = 'Internal Use'; 
$this->lang['internetConencted'] = 'Internet Connected.'; 
$this->lang['internetFailToConnect'] = 'Fail to connect, please check your connection settings.'; 
$this->lang['invalidLicense'] = 'Invalid License'; 
$this->lang['inventory'] = 'Inventory';
$this->lang['inventoryAccount'] = 'Inventory Account';
$this->lang['inventoryAdjustment'] = 'Inventory Adjustment'; 
$this->lang['inventoryList'] = 'Item List';
$this->lang['inventoryPreorderList'] = 'Preorder Item'; 
$this->lang['inventoryTempAccount'] = 'Temp. Inventory Account';
$this->lang['invoice'] = 'Invoice';
$this->lang['invoiceAmount'] = 'Invoice Amount';
$this->lang['invoiceCode'] = 'Invoice Code';   
$this->lang['invoiceDate'] = 'Invoice Date';  
$this->lang['invoiceId'] = 'Invoice ID';
$this->lang['invoiceIssued'] = 'Invoice Issued';  
$this->lang['invoiceNumber'] = 'Invoice Number';
$this->lang['invoiceOutstanding'] = 'Outstanding';
$this->lang['invoiceReceipt'] = 'Invoice Receipt';
$this->lang['invoiceReference'] = 'Invoice Reference'; 
$this->lang['invoiceTo'] = 'Invoice To';
$this->lang['invoiceType'] = 'Invoice Type';
$this->lang['issue'] = 'Issue';
$this->lang['issueCategory'] = 'Issue Category';
$this->lang['item'] = 'Item';
$this->lang['item(s)'] = 'Item(s)';
$this->lang['itemAdjustment'] = 'Item Adjustment'; 
$this->lang['itemAdjustmentReport'] = 'Item Adjustment Report'; 
$this->lang['itemCategory'] = 'Item Category';
$this->lang['itemChecklist'] = 'Item Checklist';
$this->lang['itemChecklist'] = 'Item Checklist';  
$this->lang['itemChecklistGroup'] = 'Checklist Group';  
$this->lang['itemCode'] = 'Item Code';   
$this->lang['itemDepotList'] = 'Item List (Depot)';
$this->lang['itemDetail'] = 'Item Detail'; 
$this->lang['itemFilter'] = 'Item Filter'; 
$this->lang['itemFilterReport'] = 'Item Filter Report'; 
$this->lang['itemHasBeenAddedToCart'] = 'Item has been added to cart';
$this->lang['itemIn'] = 'Item In'; 
$this->lang['itemInCode'] = 'Item In Code';  
$this->lang['itemInDate'] = 'Item In Date';
$this->lang['itemInReceive'] = 'Item Receive';  
$this->lang['itemInReport'] = 'Item In Report'; 
$this->lang['itemList'] = 'Item List';  
$this->lang['itemList'] = 'Item(s) List'; 
$this->lang['itemMovement'] = 'Item Movement'; 
$this->lang['itemName'] = 'Item Name';
$this->lang['itemName'] = 'Item Name';    
$this->lang['itemOrService'] = 'Item / Service'; 
$this->lang['itemOut'] = 'Item Out'; 
$this->lang['itemOutCode'] = 'Item Out Code';  
$this->lang['itemOutDate'] = 'Item Out Date'; 
$this->lang['itemOutDelivery'] = 'Item Delivery';  
$this->lang['itemOutReport'] = 'Item Out Report'; 
$this->lang['itemPackage'] = 'Item Package';    
$this->lang['itemPackageReport'] = 'Item Package Report';
$this->lang['itemReport'] = 'Item Report';
$this->lang['itemReportForMassUpload'] = 'Item Report for Mass Upload'; 
$this->lang['itemUnit'] = 'Item Unit';
$this->lang['items'] = 'Item(s)';
$this->lang['jobDate'] = 'Job Date';
$this->lang['jobInformation'] = 'Job Information'; 
$this->lang['jobOrder'] = 'Job Order'; 
$this->lang['jobHeader'] = 'Job Header'; 
$this->lang['jobOrderCategory'] = 'Job Order Category'; 
$this->lang['jobOrderCode'] = 'Job Order Code';   
$this->lang['jobOrderNumber'] = 'Job Order No.';  
$this->lang['jobOrderDate'] = 'Job Order Date'; 
$this->lang['jobOrderReport'] = 'Job Order Report'; 
$this->lang['jobOrderSummary'] = 'Job Order Summary';
$this->lang['jobType'] = 'Job Type';  
$this->lang['journalCode'] = 'Journal Code'; 
$this->lang['journalBalancing'] = 'Journal Balancing';
$this->lang['kirExpiredDate'] = 'KIR Expired Date';
$this->lang['kirNumber'] = 'KIR';
$this->lang['lastPage'] = 'Last Page';
$this->lang['lastRate'] = 'Last Rate';
$this->lang['lclmaster'] = 'LCL Master'; 
$this->lang['length'] = 'Length';
$this->lang['lengthShort'] = 'L';
$this->lang['licenseExpired'] = 'License has expired';
$this->lang['licenseIsValidUntil'] = 'License is valid until {{duedate}}';
$this->lang['licenseTaxExpiryDate'] = 'License Tax Expiry Date';  
$this->lang['licenseWillExpireInDays'] = 'License will expire in {{duedate}} days';
$this->lang['life'] = 'life';  
$this->lang['lifespan'] = 'Life Span';  
$this->lang['limited'] = 'Limited';
$this->lang['linkto'] = 'Link to';
$this->lang['livingAddress'] = 'Living Address';  
$this->lang['loading'] = 'Loading';
$this->lang['location'] = 'Location';
$this->lang['locationCategory'] = 'Location Category';
$this->lang['locationReport'] = 'Location Report';   
$this->lang['login'] = 'Login';
$this->lang['loginHistory'] = 'Login History';
$this->lang['loginRequired'] = 'You must be log in first.';
$this->lang['loginSuccessful'] = 'Log in successful. You will be redirected to main page.';
$this->lang['logout'] = 'Logout';
$this->lang['lowStock'] = 'Low Stock'; 
$this->lang['machineNumber'] = 'Machine Number';
$this->lang['maintenance'] = 'Maintenance';  
$this->lang['maintenanceChecklist'] = 'Maintenance Checklist';  
$this->lang['maintenanceCost'] = 'Maintenance Cost';
$this->lang['margin'] = 'Margin';
$this->lang['maritalStatus'] = 'Marital Status';  
$this->lang['marketplace'] = 'Marketplace'; 
$this->lang['master'] = 'Master'; 
$this->lang['masterRates'] = 'Master Rate'; 
$this->lang['maturity'] = 'Maturity'; 
$this->lang['max'] = 'Max.';
$this->lang['maxStock'] = 'Max. Stock';
$this->lang['mbl'] = 'MBL';
$this->lang['mblNumber'] = 'MBL Number';
$this->lang['measurement'] = 'Measurement';
$this->lang['socialMedia'] = 'Media Social';
$this->lang['memoDocuments'] = 'Memo';
$this->lang['message'] = 'Message';
$this->lang['mileage'] = 'Mileage';  
$this->lang['mileageMaintenance'] = 'Mileage Maintenance';   
$this->lang['mileageNextDue'] = 'Mileage Next Due';   
$this->lang['minStock'] = 'Min. Stock';
$this->lang['minute'] = 'minute';  
$this->lang['mobilePhone'] = 'Mobile Phone';
$this->lang['module'] = 'Module';
$this->lang['modulePrivileges'] = 'Module Privileges';
$this->lang['month'] = 'Month';
$this->lang['monthlySalesReport'] = 'Monthly Sales Report'; 
$this->lang['monthlySummaryReport'] = 'Monthly Summary Report'; 
$this->lang['movement'] = 'Movement';
$this->lang['multiPointJobOrder'] = 'Multi Point Job Order';
$this->lang['name'] = 'Name';
$this->lang['nameOfCost'] = 'Name of Cost';   
$this->lang['nameOfRate'] = 'Name of Rate'; 
$this->lang['nationality'] = 'Nationality';  
$this->lang['navigate'] = 'Navigate'; 
$this->lang['newPassword'] = 'New Password';
$this->lang['newPasswordConfirmation'] = 'New Password Confirmation';
$this->lang['newQty'] = 'New Qty'; 
$this->lang['newSerialNumber'] = 'New Serial Number';
$this->lang['news'] = 'News';
$this->lang['newsCategory'] = 'News Category'; 
$this->lang['newsContent'] = 'News Content';  
$this->lang['nextPage'] = 'Next Page';
$this->lang['noDataFound'] = 'No data found.';
$this->lang['noDescriptionAvailable'] = 'No description available.'; 
$this->lang['normalPrice'] = 'Normal Price';
$this->lang['note'] = 'Note';
$this->lang['notificationSuccessMessage'] = 'We will email you when the item has arrived.';
$this->lang['notifyMe'] = 'Notify me when available.';
$this->lang['number'] = 'No.';  
$this->lang['oil'] = 'oil';    
$this->lang['oilFilter'] = 'Oil Filter';  
$this->lang['oilIn'] = 'oil in';  
$this->lang['oilOut'] = 'oil out'; 
$this->lang['oilType'] = 'Oil Type';
$this->lang['oldPassword'] = 'Old Password';
$this->lang['open'] = 'Open';   
$this->lang['openingBalance'] = 'Opening Balance'; 
$this->lang['operationalAR'] = 'Operational AR'; 
$this->lang['operationalCost'] = 'Operational Cost'; 
$this->lang['opsCashBank'] = 'Ops. Cash/Bank';
$this->lang['order'] = 'Order';
$this->lang['orderList'] = 'Order List';
$this->lang['orderNumber'] = 'Order Number'; 
$this->lang['orderSheet'] = 'Order Sheet';  
$this->lang['orderedQty'] = 'Ordered Qty'; 
$this->lang['origin'] = 'Origin'; 
$this->lang['otherCost'] = 'Other Cost'; 
$this->lang['otherDocuments'] = 'Other Documents';
$this->lang['otherRevenue'] = 'Other Revenue'; 
$this->lang['others'] = 'Others';
$this->lang['othersInformation'] = 'Others Information';
$this->lang['othersOption'] = 'Others Option'; 
$this->lang['othersPosition'] = 'Others Position';
$this->lang['ourProducts'] = 'Our Products';
$this->lang['out'] = 'out';  
$this->lang['outOfStock'] = 'Out of Stock';
$this->lang['outsource'] = 'Outsource'; 
$this->lang['outsourceCost'] = 'Outsource Cost'; 
$this->lang['outsourceCostSummary'] = 'Outsource Cost Summary';
$this->lang['outsourceFee'] = 'Outsource Fee'; 
$this->lang['outstanding'] = 'Outstanding';
$this->lang['overStock'] = 'Over Stock';
$this->lang['overdueAccountsPayable'] = 'Overdue Accounts Payable';
$this->lang['overdueAccountsReceivable'] = 'Overdue Accounts Receivable';
$this->lang['ownerInformation'] = 'Owner Information';  
$this->lang['packageName'] = 'Package Name';
$this->lang['page'] = 'Page';
$this->lang['pageName'] = 'Page Name';
$this->lang['paidTo'] = 'Paid To'; 
$this->lang['parent'] = 'parent';
$this->lang['partChange'] = 'Changed Part';   
$this->lang['partialInvoice'] = 'Partial Invoice';  
$this->lang['partialShipment'] = 'Partial Shipment';
$this->lang['party'] = 'Party'; 
$this->lang['password'] = 'Password';
$this->lang['passwordConfirmation'] = 'Password Confirmation'; 
$this->lang['pawnSalesOrder'] = 'Sales Order';
$this->lang['pay'] = 'Pay';  
$this->lang['payableTax23AgingReport'] = 'Payable Tax 23 Aging Report';
$this->lang['payableTax23'] = 'Payable Tax 23';
$this->lang['payableTax23Payment'] = 'Payable Tax 23 Payment'; 
$this->lang['payableTax23PaymentReport'] = 'Payable Tax 23 Payment Report'; 
$this->lang['payableTax23Report'] = 'Payable Tax23 Report';
$this->lang['payingOffAmount'] = 'Paying Off Amount';
$this->lang['payment'] = 'Payment';
$this->lang['paymentAmount'] = 'Payment Amount'; 
$this->lang['paymentCode'] = 'Payment Code'; 
$this->lang['paymentConfirmation'] = 'Payment Confirmation';
$this->lang['paymentConfirmationSuccessful'] = 'Your confirmation has been sent and we will process as soon as possible.'; 
$this->lang['paymentDate'] = 'Payment Date';
$this->lang['paymentDetail'] = 'Payment Detail'; 
$this->lang['paymentDiscount'] = 'Payment Discount'; 
$this->lang['paymentMethod'] = 'Payment Method';
$this->lang['paymentRounding'] = 'Selisih Pembulatan'; 
$this->lang['perDocument'] = 'Per Document';   
$this->lang['perItem'] = 'Per Item';    
$this->lang['period'] = 'Period';
$this->lang['personalInformation'] = 'Personal Information';
$this->lang['personincharge'] = 'Person in Charge'; 
$this->lang['phone'] = 'Phone';
$this->lang['photo'] = 'Photo';
$this->lang['photoID'] = 'Photo ID';
$this->lang['placeAndDateOfBirth'] = 'Place, Date of Birth'; 
$this->lang['placeOfBirth'] = 'Place of Birth'; 
$this->lang['planner'] = 'Planner';
$this->lang['pleaseReopenAndSaveTheData']= 'Please re-open and save the data';
$this->lang['pleasestarttyping']= 'Please start typing ...'; 
$this->lang['poCode'] = 'PO Code'; 
$this->lang['poList'] = 'PO List';
$this->lang['poPrice'] = 'PO Price';
$this->lang['point'] = 'Point';
$this->lang['pointReport'] = 'Point Report';
$this->lang['pointValue'] = 'Point Value';
$this->lang['pointofsales'] = 'Point of Sales';
$this->lang['port'] = 'Port'; 
$this->lang['port'] = 'Port'; 
$this->lang['portList'] = 'Port List';  
$this->lang['portfolio'] = 'Portfolio';
$this->lang['portfolioCategory'] = 'Portfolio Category';
$this->lang['position'] = 'Position'; 
$this->lang['preorder'] = 'Pre-Order';
$this->lang['preorderSales'] = 'Preorder Sales';
$this->lang['prepaidTax23AgingReport'] = 'Prepaid Tax 23 Aging Report';
$this->lang['prepaidTax23'] = 'Prepaid Tax 23';
$this->lang['prepaidTax23Payment'] = 'Prepaid Tax 23 Payment'; 
$this->lang['prepaidTax23PaymentReport'] = 'Prepaid Tax 23 Payment Report'; 
$this->lang['prepaidTax23Report'] = 'Prepaid Tax23 Report';
$this->lang['prevQty'] = 'Prev. Qty'; 
$this->lang['previousPage'] = 'Previous Page';
$this->lang['price'] = 'Price';
$this->lang['priceAdjustment'] = 'Price Adjustment'; 
$this->lang['priceExcludesTax'] = 'Price excludes tax';
$this->lang['pricePerUnit'] = 'Harga Satuan';  
$this->lang['pricelist'] = 'Pricelist'; 
$this->lang['print'] = 'Print';
$this->lang['printCashOutVoucher'] = 'Print Cash Out Voucher'; 
$this->lang['printCashOutRequest'] = 'Print Cash Out Request'; 
$this->lang['printCompleteForm'] = 'Print Complete Form';
$this->lang['printDeliveryNote'] = 'Print Delivery Note';
$this->lang['printInvoice'] = 'Print Invoice';
$this->lang['printReceipt'] = 'Print Receipt'; 
$this->lang['printTransaction'] = 'Print Transaction';
$this->lang['printWorkOrder'] = 'Print Work Order'; 
$this->lang['privileges'] = 'Privileges'; 
$this->lang['productAndService'] = 'Products & Services'; 
$this->lang['productCategories'] = 'Product Categories';
$this->lang['productDescription'] = 'Product Description';
$this->lang['productInformation'] = 'Product Information'; 
$this->lang['productManagement'] = 'Product Management';
$this->lang['products'] = 'Products';
$this->lang['profile'] = 'Profile';
$this->lang['profit'] = 'Profit';
$this->lang['profitByBrand'] = 'Profit by Brand';
$this->lang['profitByCategory'] = 'Profit by Category';
$this->lang['profitByItem'] = 'Profit by Item';
$this->lang['profitLoss'] = 'Profit / Loss'; 
$this->lang['progressInformation'] = 'Progress Information';
$this->lang['promo'] = 'Promo';
$this->lang['promoAndCampaign'] = 'Promo & Campaign';
$this->lang['promoItem'] = 'Promo Item'; 
$this->lang['promoTitle'] = 'This Week Promo';
$this->lang['publishDate'] = 'Publish Date'; 
$this->lang['purchase'] = 'Purchase';
$this->lang['purchaseOrder'] = 'Purchase Order';
$this->lang['purchaseOrderExport'] = 'Purchase Order Export';
$this->lang['purchaseOrderImport'] = 'Purchase Order Import';
$this->lang['purchaseReceive'] = 'Purchase Receive';
$this->lang['purchaseReceiveReport'] = 'Purchase Receive Report';
$this->lang['purchaseRefund'] = 'Purchase Refund';
$this->lang['purchaseRefundReport'] = 'Purchase Refund Report';  
$this->lang['purchaseRetailDiscount'] = 'Purchase Discount (Retail)'; 
$this->lang['purchaseServiceDiscount'] = 'Purchase Discount (Service)'; 
$this->lang['purchaseReturn'] = 'Purchase Return';
$this->lang['purchasing'] = 'Purchasing';
$this->lang['purchasingCost'] = 'Purchasing Cost'; 
$this->lang['qoh'] = 'QOH';
$this->lang['qty'] = 'Qty';
$this->lang['qtyBom'] = 'Qty BOM';
$this->lang['qtyUsed'] = 'Qty Used';
$this->lang['quantity'] = 'Quantity';
$this->lang['quickSearch'] = 'Quick Search';
$this->lang['rate'] = 'Rate';   
$this->lang['rateList'] = 'Rate List';   
$this->lang['rawItemWarehouse'] = 'Raw Item Warehouse';
$this->lang['readMore'] = 'Read More'; 
$this->lang['realization'] = 'Realization';   
$this->lang['reasonToClaim'] = 'Reason to Claim'; 
$this->lang['receivedQty'] = 'Received Qty';
$this->lang['recipient'] = 'Recipient';
$this->lang['refCode'] = 'Ref. Code';
$this->lang['refDate'] = 'Ref. Date';
$this->lang['refTransactionDate'] = 'Transaction Ref. Date'; 
$this->lang['reference'] = 'Reference'; 
$this->lang['poReference'] = 'PO Reference';  
$this->lang['refresh'] = 'Refresh'; 
$this->lang['register'] = 'Register' ;
$this->lang['registration'] = 'Registration';
$this->lang['registrationReActivation'] = 'If you have previously registered, you do not need to register again.<br>Please click <a href="/resend-activation" title="Resend Activation">this link</a> to resend your activation email.'; 
$this->lang['registrationSuccessMessage'] = 'Your registration has been complete. You will receive email and email with the activation information.';
$this->lang['reimburse'] = 'Reimburse'; 
$this->lang['relation'] = 'Relation';  
$this->lang['religion'] = 'Religion'; 
$this->lang['replacement'] = 'Replacement';
$this->lang['replacementItem'] = 'Replacement Item';
$this->lang['report'] = 'Report'; 
$this->lang['requestDemo'] = 'Request Demo';
$this->lang['resend'] = 'Resend'; 
$this->lang['resendActivation'] = 'Resend Activation';
$this->lang['resendActivationMessage'] = 'Please enter the email address you used to register with us.';
$this->lang['resendActivationSuccessMessage'] = 'Your request has been submitted successfully. An email has been sent to you with instructions to activate your account. Thank You.';
$this->lang['resetCost'] = 'Reset Cost';
$this->lang['resetEvery'] = 'Reset Every';
$this->lang['resetPassword'] = 'Reset Password';
$this->lang['resetPasswordSuccessMessage'] = 'Your request has been submitted successfully. An email has been sent to you with instructions to reset your password. Thank You.';
$this->lang['resetPasswordSuccessful'] = 'You have successfully reset your password. A new password has been sent to your email.';
$this->lang['resetType'] = 'Reset Type';
$this->lang['resistance'] = 'resistance';  
$this->lang['restockList'] = 'Restock List'; 
$this->lang['resultItemWarehouse'] = 'Result Item Warehouse';
$this->lang['retail'] = 'Retail';
$this->lang['retainedEarning'] = 'Retained Earning'; 
$this->lang['revenue'] = 'Revenue';
$this->lang['revenueAccount'] = 'Revenue Account';
$this->lang['reverseClosingPeriod'] = 'Reverse Closing Period'; 
$this->lang['reward'] = 'Reward';
$this->lang['rewardPoints'] = 'Reward Points'; 
$this->lang['rma'] = 'RMA';
$this->lang['roleTemplate'] = 'Role Template'; 
$this->lang['route'] = 'Route';
$this->lang['runningNumber'] = 'Running Number';
$this->lang['saidAmount'] = 'Said Amount'; 
$this->lang['sales'] = 'Sales';
$this->lang['salesCommission'] = 'Sales Commission'; 
$this->lang['salesCommissionPayment'] = 'Sales Commission Payment'; 
$this->lang['salesCommissionPaymentReport'] = 'Sales Commission Payment Report'; 
$this->lang['salesCommissionReport'] = 'Sales Commission Report'; 
$this->lang['salesDelivery'] = 'Sales Delivery';
$this->lang['salesDeliveryReport'] = 'Sales Delivery Report';
$this->lang['salesDiscount'] = 'Sales Discount';
$this->lang['salesGraph'] = 'Sales Graph';
$this->lang['salesInformation'] = 'Sales Information';
$this->lang['salesInvoice'] = 'Sales Invoice'; 
$this->lang['salesInvoiceReport'] = 'Sales Invoice Report'; 
$this->lang['salesOrder'] = 'Sales Order';
$this->lang['salesOrderDomestic'] = 'Sales Order Domestic';  
$this->lang['salesOrderExport'] = 'Sales Order Export'; 
$this->lang['salesOrderReport'] = 'Sales Order Report';
$this->lang['salesOrderByGroupReport'] = 'Sales Order By Group Report'; 
$this->lang['salesRetail'] = 'Sales (Retail)'; 
$this->lang['salesRetailDiscount'] = 'Sales Discount (Retail)'; 
$this->lang['salesReturn'] = 'Sales Return';
$this->lang['salesReturnReport'] = 'Sales Return Report';
$this->lang['salesService'] = 'Sales (Service)'; 
$this->lang['salesServiceDiscount'] = 'Sales Discount (Service)'; 
$this->lang['salesTransaction'] = 'Sales';
$this->lang['salesman'] = 'Salesman'; 
$this->lang['save'] = 'Save';
$this->lang['saveAndProceed'] = 'Save & Proceed';  
$this->lang['saveAndProceedTo'] = 'Save & Proceed to';    
$this->lang['say'] = 'Say';
$this->lang['seal'] = 'Seal';
$this->lang['sealNumber'] = 'Seal Number'; 
$this->lang['search'] = 'Search';
$this->lang['searchFilter'] = 'Search Filter';
$this->lang['searchProduct'] = 'Search Product';
$this->lang['searchResult'] = 'Search Results'; 
$this->lang['second'] = 'Second';
$this->lang['security'] = 'Security';  
$this->lang['securityPrivileges'] = 'Security Privileges'; 
$this->lang['selectAll'] = 'Select All';
$this->lang['selling'] = 'Selling';  
$this->lang['sellingPrice'] = 'Selling Price';
$this->lang['sellingRate'] = 'Selling Rate'; 
$this->lang['send'] = 'Send'; 
$this->lang['serialNumber'] = 'Serial Number'; 
$this->lang['serialNumberReplacement'] = 'Serial Number Replacement'; 
$this->lang['series'] = 'Series';  
$this->lang['service'] = 'Service';
$this->lang['serviceAndCostCategory'] = 'Service & Cost Category'; 
$this->lang['serviceBooking'] = 'Service Booking';
$this->lang['serviceCategory'] = 'Service Category'; 
$this->lang['serviceCategory'] = 'Service Category';    
$this->lang['serviceList'] = 'Service List'; 
$this->lang['serviceManagement'] = 'Service Management';
$this->lang['serviceName'] = 'Service Name';
$this->lang['serviceOrder'] = 'Service Order'; 
$this->lang['serviceOrderCategory'] = 'Service Order Category'; 
$this->lang['serviceOrderInvoiceReport'] = 'Sales Invoice Report'; 
$this->lang['serviceOrderReport'] = 'Service Order Report';
$this->lang['serviceWorkOrder'] = 'Service Work Order'; 
$this->lang['serviceWorkOrderDate'] = 'Work Order Date';  
$this->lang['services'] = 'Services'; 
$this->lang['setting'] = 'Setting';
$this->lang['settlement'] = 'Settlement';
$this->lang['settlementType'] = 'Settlement Type'; 
$this->lang['sex'] = 'Sex';  
$this->lang['shipment'] = 'Shipment';
$this->lang['shipmentReceipt'] = 'Shipment Receipt';
$this->lang['shipper'] = 'Shipper'; 
$this->lang['shippingAddress'] = 'Shipping Address';   
$this->lang['shippingCompany'] = 'Shipping Company';     
$this->lang['shippingCompanyList'] = 'Shipping Company List';   
$this->lang['shippingCost'] = 'Shipping Cost'; 
$this->lang['shippingCourier'] = 'Shipping Courier'; 
$this->lang['shippingDate'] = 'Shipping Date'; 
$this->lang['shippingFee'] = 'Shipping Fee';
$this->lang['shippingInformation'] = 'Shipping Information';
$this->lang['shippingLabel'] = 'Shipping Label'; 
$this->lang['shippingLine'] = 'Shipping Line'; 
$this->lang['shippingReceipt'] = 'Shipping Receipt'; 
$this->lang['shopId'] = 'Shop ID';
$this->lang['shoppingOrder'] = 'Shopping Order'; 
$this->lang['shortDescription'] = 'Short Description';
$this->lang['showAll'] = 'Show All';
$this->lang['showDetail'] = 'Show Detail';
$this->lang['showIn'] = 'Show In';   
$this->lang['showInPaymentConfirmation'] = 'Show in Payment Confirmation'; 
$this->lang['showInvoice'] = 'Show Invoice';
$this->lang['si'] = 'S / I'; 
$this->lang['size'] = 'Size';
$this->lang['slot'] = 'Slot';
$this->lang['snInformation'] = 'SN Information';
$this->lang['snMovementReport'] = 'SN Movement Report'; 
$this->lang['soCode'] = 'SO Code'; 
$this->lang['soldDate'] = 'Sold Date';
$this->lang['sourceWarehouse'] = 'Source Warehouse';
$this->lang['specification'] = 'Specification';
$this->lang['specificationFile'] = 'Specification File';
$this->lang['speed'] = 'Speed';
$this->lang['spkDocuments'] = 'Work Order Document';
$this->lang['status'] = 'Status';
$this->lang['stepProgress'] = 'Step Progress'; 
$this->lang['stnkExpiredDate'] = 'License Expired Date';
$this->lang['stnkNumber'] = 'License Number (STNK)';
$this->lang['stock'] = 'Stock';
$this->lang['stockCardDepotReport'] = 'Depot\'s Stock Card Report';
$this->lang['stockCardReport'] = 'Stock Card Report';
$this->lang['stockInformation'] = 'Stock Information';
$this->lang['store'] = 'Store'; 
$this->lang['storeLocation'] = 'Store Location';
$this->lang['storeName'] = 'Store Name';
$this->lang['stuffing'] = 'Stuffing';
$this->lang['stuffingAndDestuffingDateTime'] = 'Stuffing / Destuffing Date';
$this->lang['stuffingDate'] = 'Stuffing Date';
$this->lang['stuffingDestuffingInformation'] = 'Stuffing / Destuffing Information';
$this->lang['stuffingInformation'] = 'Stuffing Information'; 
$this->lang['destuffingInformation'] = 'Destuffing Information';
$this->lang['subject'] = 'Subject';
$this->lang['submit'] = 'Submit';
$this->lang['subtotal'] = 'Subtotal';
$this->lang['supplier'] = 'Supplier';
$this->lang['supplierDownpayment'] = 'Supplier Downpayment'; 
$this->lang['supplierDownpaymentReport'] = 'Supplier Downpayment Report'; 
$this->lang['supplierReport'] = 'Supplier Report';
$this->lang['tag'] = 'Tag'; 
$this->lang['tariffList'] = 'Rate List';
$this->lang['tax'] = 'Tax';
$this->lang['tax23'] = 'PPH 23';
$this->lang['taxIdentificationNumber'] = 'Tax Identification Number'; 
$this->lang['taxIn'] = 'Tax In'; 
$this->lang['taxOut'] = 'Tax Out'; 
$this->lang['taxRegistrationAddress'] = 'Tax Registration Address'; 
$this->lang['taxRegistrationName'] = 'Tax Registration Name'; 
$this->lang['taxRegistrationNumber'] = 'Tax Registration Number'; 
$this->lang['technician'] = 'Technician';    
$this->lang['technicianSolutions'] = 'Technician Solutions';   
$this->lang['tempInventory'] = 'Temp. Inventory';
$this->lang['temperature'] = 'Temperature';  
$this->lang['temperatureAfter'] = 'AC Temperature (After)';  
$this->lang['temperatureBefore'] = 'AC Temperature (Before)';  
$this->lang['termOfPaymentName'] = 'TOP Name'; 
$this->lang['terminalList'] = 'Terminal List'; 
$this->lang['termofpayment'] = 'Term of Payment';
$this->lang['termsAndConditions'] = 'Terms And Conditions'; 
$this->lang['termsandagreements'] = 'Terms and Agreements';
$this->lang['testimonial'] = 'Testimonial';
$this->lang['thisUserHasNoHistoryOfLogin'] = 'This user has no history of login'; 
$this->lang['tidExpiredDate'] = 'TID Expired Date';
$this->lang['tidNumber'] = 'TID';
$this->lang['timeLog'] = 'Time Log';
$this->lang['title'] = 'Title'; 
$this->lang['toAccount'] = 'To Account'; 
$this->lang['token'] = 'Token';
$this->lang['tools'] = 'Tools';
$this->lang['top'] = 'TOP';
$this->lang['topCustomers'] = 'Top Customers';
$this->lang['total'] = 'Total';
$this->lang['totalBuying'] = 'Total Buying'; 
$this->lang['totalCOGS'] = 'Total COGS';
$this->lang['totalCommission'] = 'Total Komisi';
$this->lang['totalCost'] = 'Total Cost';  
$this->lang['totalData'] = 'Total Data';
$this->lang['totalDifference'] = 'Total Difference'; 
$this->lang['totalDiscount'] = 'Total Discount'; 
$this->lang['totalExpense'] = 'Total Expense'; 
$this->lang['totalIncome'] = 'Total Income'; 
$this->lang['totalPayment'] = 'Total Payment'; 
$this->lang['totalPoint'] = 'Total Point';
$this->lang['totalSales'] = 'Total Sales';  
$this->lang['totalTrip'] = 'Total Trip';
$this->lang['totalVolume'] = 'Total Volume'; 
$this->lang['totalWeight'] = 'Total Weight';
$this->lang['transactionCode'] = 'Transaction Code'; 
$this->lang['transactionDate'] = 'Transaction Date'; 
$this->lang['transactionHistory'] = 'Transaction History'; 
$this->lang['transactionInformation'] = 'Transaction Information'; 
$this->lang['transactionType'] = 'Transaction Type'; 
$this->lang['transactionUnit'] = 'Transaction Unit'; 
$this->lang['transhipment'] = 'Transhipment'; 
$this->lang['trip'] = 'Trip';
$this->lang['trucking'] = 'Trucking';   
$this->lang['truckingCashFlowReportReport'] = 'Trucking Cash Flow Report';
$this->lang['truckingCostCashIn'] = 'Trucking Cost Cash In'; 
$this->lang['truckingCostCashOut'] = 'Trucking Cost Cash Out'; 
$this->lang['truckingCostCashOutReport'] = 'Trucking Cost Cash Out Report'; 
$this->lang['truckingCostList'] = 'Trucking Cost List'; 
$this->lang['truckingFee'] = 'Trucking Fee';
$this->lang['truckingRate'] = 'Trucking Rate';   
$this->lang['truckingRateFCL'] = 'Trucking Rate FCL'; 
$this->lang['truckingServiceList'] = 'Trucking Service List'; 
$this->lang['truckingServiceOrderReport'] = 'Trucking Order Report'; 
$this->lang['truckingServiceWorkOrder'] = 'Trucking Work Order';
$this->lang['tuneupPackage'] = 'Tune Up Package';
$this->lang['type'] = 'Type'; 
$this->lang['typeOfJob'] = 'Type of Job'; 
$this->lang['typesOfFuel'] = 'Types of Fuel';  
$this->lang['typesOfOil'] = 'type of oil';  
$this->lang['underMaintenance'] = 'Under Maintenance';
$this->lang['underMarginSalesOrder'] = 'Under Margin Sales Order'; 
$this->lang['unit'] = 'Unit';
$this->lang['unitName'] = 'Unit Name';
$this->lang['unproccesedPurchaseOrder'] = 'Unproccesed Purchase Order'; 
$this->lang['unproccesedSalesOrder'] = 'Unproccesed Sales Order'; 
$this->lang['update'] = 'Update';
$this->lang['updateCost'] = 'Update Cost';
$this->lang['updatePassword'] = 'Update Password';
$this->lang['updateProgress'] = 'Update Progress';
$this->lang['updateSearchFilter'] = 'Update Search Filter';
$this->lang['url'] = 'url'; 
$this->lang['usageHistory'] = 'Usage History';
$this->lang['useInsurance'] = 'Use Insurance'; 
$this->lang['user'] = 'User'; 
$this->lang['userPrivileges'] = 'User Privileges'; 
$this->lang['username'] = 'Username'; 
$this->lang['value'] = 'Value';  
$this->lang['variableSetting'] = 'Variable Setting';
$this->lang['vat'] = 'VAT'; 
$this->lang['vendorPartNumber'] = 'Vendor Part Number';  
$this->lang['vendorWarrantyClaim'] = 'Warranty Claim (Vendor)';   
$this->lang['vendorWarrantyClaimReceive'] = 'Penerimaan Klaim Garansi (Vendor)'; 
$this->lang['verificationFailed'] = 'Verification failed'; 
$this->lang['verificationSuccessful'] = 'Verification successful'; 
$this->lang['verify'] = 'Verify'; 
$this->lang['vessel'] = 'Vessel';  
$this->lang['vesselList'] = 'Vessel List'; 
$this->lang['vesselNumber'] = 'Vessel Number';  
$this->lang['via'] = 'Via';   
$this->lang['viewOrEdit'] = 'View / Edit';
$this->lang['volume'] = 'Volume';
$this->lang['warehouse'] = 'Warehouse';
$this->lang['warehouseAccess'] = 'Warehouse Access';
$this->lang['warehousePrivileges'] = 'Warehouse Privileges';
$this->lang['warehouseTransfer'] = 'Warehouse Transfer';
$this->lang['warehouseTransferReport'] = 'Warehouse Transfer Report';
$this->lang['warrantyClaim'] = 'Warranty Claim'; 
$this->lang['warrantyClaimProgress'] = 'Warranty Claim Progress';
$this->lang['warrantyClaimProgressReport'] = 'Warranty Claim Progress Report';
$this->lang['warrantyExpiredDate'] = 'Warranty End Date';
$this->lang['warrantyPeriod'] = 'Warranty Period';
$this->lang['warrantySettlement'] = 'Warranty Settlement'; 
$this->lang['warrantyTermsAndConditions'] = 'Warranty Terms And Conditions'; 
$this->lang['webpage'] = 'Web Page';
$this->lang['websiteAccount'] = 'Website Account';
$this->lang['webstore'] = 'Webstore';
$this->lang['weight'] = 'Weight';
$this->lang['weightGrams'] = 'Weight (gr)';
$this->lang['welcome'] = 'Welcome';
$this->lang['width'] = 'Width';
$this->lang['widthShort'] = 'W';
$this->lang['workDescription'] = 'Work Description';   
$this->lang['workOrderCostReport'] = 'Work Order Cost Report'; 
$this->lang['workOrderReport'] = 'Work Order Report'; 
$this->lang['workProgress'] = 'Work Progress';
$this->lang['workshopServiceList'] = 'Workshop Service List';   
$this->lang['writeOffAccountsReceivable'] = 'Write Off Accounts Receivable'; 
$this->lang['year'] = 'Year';
$this->lang['youtube'] = 'Youtube';
$this->lang['zipcode'] = 'Zip Code';
$this->lang['dragToReorder'] = 'Drag to reorder';
$this->lang['settingsSaved'] = 'Settings saved.';
$this->lang['pleaseReopenThisTab'] = 'Please reopen this tab.';
$this->lang['purchaseOrderReport'] = 'Purchase Order Report';
$this->lang['loginLogReport'] = 'Login Log Report';
$this->lang['ipaddress'] = 'IP Address';
$this->lang['pageShort'] = 'Page'; 
$this->lang['transactionLogReport'] = 'Transaction Log Report';
$this->lang['qtyInUnit'] = 'Qty In Unit';
$this->lang['salesOrderExportReport'] = 'Sales Order Export Report'; 
$this->lang['salesOrderImportReport'] = 'Sales Order Import Report'; 
$this->lang['salesOrderInvoiceReceipt'] = 'Sales Order Invoice Receipt'; 
$this->lang['receiptDate'] = 'Receipt Date';  
$this->lang['created'] = 'Prepared'; 
$this->lang['approved'] = 'Approved'; 
$this->lang['received'] = 'Received'; 
$this->lang['messenger'] = 'Messenger';
$this->lang['salesOrderInvoiceReceiptReport'] = 'Sales Order Invoice Receipt Report';
$this->lang['maxFileSizeUpload'] = 'Max. File Size Upload'; 
$this->lang['maxSizeUploadPerFile'] = 'Max. Size Upload / File'; 
$this->lang['manage'] = 'Manage'; 
$this->lang['diskUsage'] = 'Disk Usage'; 
$this->lang['noSpaceBeingUsed'] = 'No Space Being Used'; 
$this->lang['carScheduleReport'] = 'Car Schedule Report'; 
$this->lang['conversion'] = 'Conversion';
$this->lang['conversionUnit'] = 'Conversion Unit';
$this->lang['consigneeWarehouse'] = 'Consignee Warehouse';
$this->lang['creditNote'] = 'Credit Note';
$this->lang['itemAgingReport'] = 'Item Aging Report';
$this->lang['changeItemSN'] = 'Change Item SN';
$this->lang['old'] = 'Old';
$this->lang['new'] = 'New';
$this->lang['entrustedCar'] = 'Entrusted Car';
$this->lang['apPeriod'] = 'AP Period';
$this->lang['payingSettlement'] = 'Settlement';
$this->lang['printSummary'] = 'Print Summary';
$this->lang['routineCost'] = 'Routine Cost';
$this->lang['forEach'] = 'For Each';
$this->lang['repeatEvery'] = 'Repeat Every';
$this->lang['runNow'] = 'Run Now';
$this->lang['TheProcessHasBeenRun'] = 'The process has been run';
$this->lang['allCustomer'] = 'All Customers';
$this->lang['customerPrivileges'] = 'Customer Privileges';
$this->lang['autoCode'] = 'Auto Code';
$this->lang['leasing'] = 'Leasing';
$this->lang['termOfLease'] = 'Term of Lease';
$this->lang['installmentStartDate'] = 'Installment Date';
$this->lang['startingDate'] = 'Starting Date';
$this->lang['loanAmount'] = 'Loan Amount';
$this->lang['installment'] = 'Installment';
$this->lang['assetsId'] = 'Assets ID' ;
$this->lang['vehicle'] = 'Vehicle' ;
$this->lang['itemCondition'] = 'Item Condition' ;
$this->lang['noActiveMarketplace'] = 'No active marketplace.' ;
$this->lang['jobDescription'] = 'Job Description' ;
$this->lang['notDue'] = 'Not Yet' ;
$this->lang['arPeriod'] = 'AR Period' ;
$this->lang['pod'] = 'POD' ;
$this->lang['pol'] = 'POL' ;
$this->lang['templatePurchaseItem'] = 'Purchase Item Template' ;
$this->lang['auto'] = 'Auto';
$this->lang['cn/dn'] = 'CN / DN';
$this->lang['owner'] = 'Owner';
$this->lang['shareProfit'] = 'Share Profit';
$this->lang['templateCustomer'] = 'Customer Template';
$this->lang['templateSupplier'] = 'Supplier Template';
$this->lang['chooseTemplate'] = 'Choose Template';
$this->lang['searchTemplate'] = 'Search Template';
$this->lang['notMovingStock'] = 'Not Moving Stock';
$this->lang['membership'] = 'Membership';
$this->lang['maxAttendance'] = 'Max. Attendance';
$this->lang['timeLimit'] = 'Time Limit';
$this->lang['yes'] = 'Yes';
$this->lang['no'] = 'No';
$this->lang['choose'] = 'Choose';
$this->lang['occupation'] = 'Occupation';
$this->lang['terminal'] = 'Terminal';
$this->lang['stuffingDestuffingLocation'] = 'Stuffing / Destuffing Location';
$this->lang['vehicleLicense'] = 'Vehicle License';
$this->lang['vehicleLicenseOverdue'] = 'Reminder for vehicle registration';
$this->lang['dailyTransactionSummary'] = 'Daily Transaction';
$this->lang['dailyMarketplaceTransactionSummary'] = 'Daily Marketplace Transaction';
$this->lang['iAgreeToTermsAndConditions'] = 'I agree to terms and conditions';
$this->lang['membershipAttendance'] = 'Membership Attendance';
$this->lang['membershipType'] = 'Membership Type';
$this->lang['customerMembership'] = 'Keanggotaan Pelanggan'; 
$this->lang['biodata'] = 'Biodata'; 
$this->lang['attendance'] = 'Attendance'; 
$this->lang['expiredOn'] = 'Expired On'; 
$this->lang['checkIn'] = 'Check In'; 
$this->lang['checkInSuccessful'] = 'Check In Successful'; 
$this->lang['checkInTime'] = 'Check In Time'; 
$this->lang['class'] = 'Class'; 
$this->lang['compareProducts'] = 'Compare Products'; 
$this->lang['quickView'] = 'Quick View'; 
$this->lang['subscribe'] = 'Subscribe'; 
$this->lang['searchResultFor'] = 'Search result for'; 
$this->lang['swift'] = 'SWIFT'; 
$this->lang['warranty'] = 'Warranty'; 
$this->lang['moreDetails'] = 'Detail lebih lengkap'; 
$this->lang['dontHaveAnAccountYet'] = 'Don\'t have an account yet';
$this->lang['createNewAccount'] = 'Create new account'; 
$this->lang['paymentTo'] = 'Payment to';
$this->lang['bankCode'] = 'Bank Code';
$this->lang['itemSpecification'] = 'Item Specification'; 
$this->lang['referral'] = 'Referral';
$this->lang['importAttributes'] = 'Import Attributes';
$this->lang['voucher'] = 'Voucher';
$this->lang['voucherTransaction'] = 'Voucher Transaction';
$this->lang['voucherAmount'] = 'Voucher Amount';
$this->lang['minimumTransaction'] = 'Minimum Transaction';
$this->lang['issuedQty'] = 'Issued Qty'; 
$this->lang['used'] = 'Used';
$this->lang['allowToCombine'] = 'Allow to Combine';
$this->lang['maxDiscount'] = 'Max. Discount';
$this->lang['leaveItBlankForUnlimited'] = 'Kosongkan jika tidak ada batas';
$this->lang['reseller'] = 'Reseller'; 
$this->lang['endUser'] = 'End User';
$this->lang['customerType'] = 'Customer Type';
$this->lang['criteria'] = 'Criteria';
$this->lang['startDate'] = 'Start Date';
$this->lang['endDate'] = 'End Date';
$this->lang['issued'] = 'Issued';
$this->lang['usedQty'] = 'Used Qty';
$this->lang['usedOn'] = 'Used On';
$this->lang['membershipRegistration'] = 'Membership Registration';
$this->lang['activationDate'] = 'Activation Date';
$this->lang['compare'] = 'Compare';
$this->lang['blog'] = 'Blog';
$this->lang['customerCode'] = 'Customer Code';
$this->lang['noSpecificationAvailable'] = 'No Specification Available';
$this->lang['registrationCost'] = 'Registration Cost';
$this->lang['activeVoucher'] = 'Active Voucher';
$this->lang['usedVoucher'] = 'Inactive Voucher'; 
$this->lang['nothingToCompare'] = 'Nothing to Compare';
$this->lang['productsComparison'] = 'Products Comparison';
$this->lang['GrossProfitReport'] = 'Gross Profit Report'; 
$this->lang['API'] = 'API';
$this->lang['serviceCode'] = 'Service Code';
$this->lang['adminFee'] = 'Admin Fee';
$this->lang['costRateReport'] = 'Cost Rate Report';
$this->lang['sellingRateReport'] = 'Selling Rate Report';
$this->lang['currencyPreference'] = 'Currency Preference';
$this->lang['salesType'] = 'Sales Type';
$this->lang['partnershipType'] = 'Partnership Type';
$this->lang['onCall'] = 'On Call';
$this->lang['contract'] = 'Contract';
$this->lang['apEmployeeCommissionReport'] = 'Employee Commission Report';
$this->lang['vehicleChecklist'] = 'Vehicle Checklist';
$this->lang['youDontHaveAnyJobYet'] = 'You dont have any job yet';
$this->lang['WorkOrder'] = 'Work Order';
$this->lang['vehicleAvailabilityReport'] = 'Vehicle Availability Report'; 
$this->lang['available'] = 'Available';
$this->lang['availableOnly'] = 'Available Only';
$this->lang['goodCondition'] = 'Good';
$this->lang['badCondition'] = 'Bad';
$this->lang['jobList'] = 'Job List';
$this->lang['vehicleCondition'] = 'Vehicle Condition';
$this->lang['proceed'] = 'Proceed';
$this->lang['pleaseInputWOCodeYouWantToProceed'] = 'Silahkan mengisi No. SPK yang hendak diproses';
$this->lang['verificationCode'] = 'Verification code'; 
$this->lang['SNAgingReport'] = 'Serial Number Aging Report'; 
$this->lang['arapNetting'] = 'AR / AP Netting'; 
$this->lang['employeeARAP'] = 'Employee AR / AP';  
$this->lang['employeeARAPNetting'] = 'Employee AR / AP Netting'; 
$this->lang['netting'] = 'Netting'; 
$this->lang['purchaseOrderExportReport'] = 'Purchase Order Export Report'; 
$this->lang['purchaseOrderImportReport'] = 'Purchase Order Import Report'; 
$this->lang['commissionPeriod'] = 'Commission Period'; 
$this->lang['showInWebstore'] = 'Show In Webstore'; 
$this->lang['carMaintenanceReport'] = 'Car Maintenance Report'; 
$this->lang['storefront'] = 'Storefront'; 
$this->lang['jobsDate'] = 'Jobs Date'; 
$this->lang['syncToAllMarketplaces'] = 'Sync to all marketplace(s)'; 
$this->lang['ritaseSummaryReport'] = 'Ritase Summary Report';
$this->lang['paymentInformation'] = 'Payment Information';
$this->lang['APCardReport'] = 'AP Card Report';
$this->lang['ARCardReport'] = 'AR Card Report';
$this->lang['voucherCashInCode'] = 'Voucher Cash In Code';
$this->lang['voucherCashOutCode'] = 'Voucher Cash Out Code';
$this->lang['counterAccount'] = 'Counter Account';
$this->lang['counterCashBank'] = 'Ayat Silang';
$this->lang['AROutstanding'] = 'AR Outstanding';
$this->lang['attention'] = 'Attention';
$this->lang['PIC'] = 'PIC';  
$this->lang['initialCost'] = 'Initial Cost';
$this->lang['monthlyCost'] = 'Monthly Cost';
$this->lang['salesOrderRental'] = 'Rental Order';
$this->lang['rentalSales'] = 'Rental Sales'; 
$this->lang['woDate'] = 'WO Date';
$this->lang['woStartDate'] = 'WO Start';
$this->lang['woEndDate'] = 'WO End';
$this->lang['media'] = 'Media';  
$this->lang['jobDetails'] = 'Job Details'; 
$this->lang['supportWorkOrder'] = 'Support Work Order';
$this->lang['installationWorkOrder'] = 'Installation Work Order';
$this->lang['ticketSupport'] = 'Ticket Support';
$this->lang['technician'] = 'Technician';
$this->lang['urgency'] = 'Urgency';
$this->lang['timeUnit'] = 'Time Unit';
$this->lang['lostPrice'] = 'Lost Price';
$this->lang['rentPrice'] = 'Rent Price';
$this->lang['stagesProcess'] = 'Tahapan Pekerjaan';
$this->lang['purchaseRequest'] = 'Purchase Request';
$this->lang['mainAccount'] = 'Main Account';
$this->lang['sid'] = 'SID';
$this->lang['salesRentalQuotation'] = 'Sales Rental Quotation';
$this->lang['quotationName'] = 'Quotation Name';
$this->lang['quotation'] = 'Quotation';
$this->lang['requestAmount'] = 'Request Amount';
$this->lang['startTime'] = 'Start Time';
$this->lang['endTime'] = 'End Time';
$this->lang['review'] = 'Review';
$this->lang['partners'] = 'Partners';
$this->lang['jobOpportunities'] = 'Lowongan Pekerjaan';
$this->lang['confirmedDate'] = 'Confirmed Date';
$this->lang['language'] = 'Language';
$this->lang['allLanguages'] = 'All Languages';
$this->lang['featured'] = 'Featured';
$this->lang['managementTeam'] = 'Management Team';
$this->lang['cashAndBankVoucherReport'] = 'Cash & Bank Voucher Report'; 
$this->lang['cashBankNumber'] = 'Cash Bank Number';
$this->lang['updating'] = 'Updating';
$this->lang['printQRCode'] = 'print QR Code';
$this->lang['deliveryWorkOrder'] = 'Delivery Work Order';
$this->lang['subscriptionStatus'] = 'Subscription Status';
$this->lang['invoiceTaxNumber'] = 'Invoice Tax Number';
$this->lang['paidAmount'] = 'Paid Amount';
$this->lang['jobQueue'] = 'Job queue';
$this->lang['campaign'] = 'Campaign';
$this->lang['BAST'] = 'Record of Transfer';
$this->lang['cashAndBankVoucher'] = 'Cash & Bank Voucher';
$this->lang['cashInVoucher'] = 'Cash/Bank In Voucher';
$this->lang['cashOutVoucher'] = 'Cash/Bank Out Voucher';
$this->lang['sender'] = 'Sender';
$this->lang['minimumStatusRequired'] = 'Minimum status required';
$this->lang['freight'] = 'Freight';
$this->lang['cityReport'] = 'City Report';
$this->lang['cityCategoryReport'] = 'City Category Report';
$this->lang['serviceReport'] = 'Service Report';
$this->lang['dumper'] = 'Dumper';
$this->lang['project'] = 'Project';
$this->lang['distance'] = 'Distance';
$this->lang['destinationSite'] = 'Destination Site';
$this->lang['termination'] = 'Termination'; 
$this->lang['representedby'] = 'Represented By';
$this->lang['department'] = 'Department'; 
$this->lang['invoiceRecurring'] = 'Invoice Recurring';
$this->lang['printHeader'] = 'Print Header';
$this->lang['marketplaceLogReport'] = 'Marketplace Log Report'; 
$this->lang['success'] = 'Success';
$this->lang['failed'] = 'Failed';
$this->lang['voucherNumber'] = 'Voucher No.';
$this->lang['qor'] = 'QOR';
$this->lang['salesInvoiceRental'] = 'Sales Invoice (Rental)';
$this->lang['quotationCode'] = 'Quotation Code';
$this->lang['requestPickup'] = 'Request Pickup';
$this->lang['delivered'] = 'Delivered'; 
$this->lang['syncMarketplace'] = 'Marketplace Sync'; 
$this->lang['forEachWarehouse'] = 'For each warehouse'; 
$this->lang['voyage'] = 'Voyage'; 
$this->lang['containerType'] = 'Container Type';
$this->lang['feeder'] = 'Feeder';
$this->lang['stackArea'] = 'Stack Area';
$this->lang['stuffingIn'] = 'Stuffing In';
$this->lang['stuffingOut'] = 'Stuffing Out';
$this->lang['vendor'] = 'Vendor';
$this->lang['rentalTimesheetReport'] = 'Rental Timesheet Report'; 
$this->lang['time'] = 'Time';
$this->lang['salesOrderDumperReport'] = 'Sales Order Dumper Report'; 
$this->lang['jobOrderHeaderExport'] = 'Job Header Export'; 
$this->lang['jobOrderHeaderExportReport'] = 'Job Header Export Report'; 
$this->lang['jobOrderHeaderImport'] = 'Job Header Import'; 
$this->lang['jobOrderHeaderImportReport'] = 'Job Header Import Report'; 
$this->lang['stackArea'] = 'Stack Area';
$this->lang['transaction'] = 'Transaction';
$this->lang['blNumber'] = 'BL Number';
$this->lang['next'] = 'Next';
$this->lang['course'] = 'Course';
$this->lang['courseList'] = 'Course List';
$this->lang['quiz'] = 'Quiz';
$this->lang['question'] = 'Question';
$this->lang['answer'] = 'Answer';
$this->lang['courseCategory'] = 'Course Category';
$this->lang['containerType'] = 'Container Type';
$this->lang['leaveItBlankForDefaultItemName'] = 'Kosongkan jika mengikuti nama barang diatas';
$this->lang['garageCashVoucherReport'] = 'Workshop Cash Voucher Report';  
$this->lang['maintenanceCashVoucherReport'] = 'Laporan Kas Maintenance'; 
$this->lang['printFormJO'] = 'Print Form JO';
$this->lang['cashBankIn'] = 'Cash / Bank In';
$this->lang['revenueList'] = 'Revenue List'; 
$this->lang['cash/bank'] = 'Cash / Bank'; 
$this->lang['temporaryAccount'] = 'Temporary Account'; 
$this->lang['profitLossRate'] = 'Profit / Loss Currency Rate'; 
$this->lang['senderOrRecipient'] = 'Sender / Recipient'; 
$this->lang['rememberMe'] = 'Remember me'; 
$this->lang['itemReturn'] = 'Item return'; 
$this->lang['cashAdvance'] = 'Cash Advance'; 
$this->lang['cashAdvanceRealization'] = 'Cash Advance Realization'; 
$this->lang['cashBankInReport'] = 'Cash / Bank In Report';  
$this->lang['settlementAccount'] = 'Settlement Account';  
$this->lang['recipientAccount'] = 'Recipient Account';   
$this->lang['cashAdvanceReport'] = 'Cash Advance Report';
$this->lang['cashAdvanceAmount'] = 'Cash Advance Amount';
$this->lang['cashAdvAmount'] = 'Cash Adv. Amount';
$this->lang['cashAdvanceRealizationReport'] = 'Cash Advance Realization Report';
$this->lang['combo'] = 'Combo';
$this->lang['realizationDate'] = 'Realization Date';
$this->lang['prepaidTaxReceiptCode'] = 'Receipt Code';
$this->lang['prepaidTaxReceiptDate'] = 'Receipt Date';
$this->lang['prepaidTaxReceiptAmount'] = 'Receipt Amount';
$this->lang['trialBalanceReport'] = 'Trial Balance Report';
$this->lang['beginningBalance'] = 'Beginning Balance';
$this->lang['endingBalance'] = 'Ending Balance';
$this->lang['overPaid'] = 'Over Paid';
$this->lang['detailsNote'] = 'Details Note';
$this->lang['supplierCommission'] = 'Supplier Commission';
$this->lang['commissionAccount'] = 'Commission Account';
$this->lang['withholdingCode'] = 'Withholding Code';
$this->lang['dateRef'] = 'Date Ref.';
$this->lang['activityDate'] = 'Activity Date';
$this->lang['itemConversion'] = 'Item Conversion' ;
$this->lang['uninvoicedSalesOrderExportReport'] = 'Uninvoiced Sales Order Export Report';
$this->lang['uninvoicedSOExportReport'] = 'Uninvoiced SO Export Report';
$this->lang['invoiced'] = 'Invoiced';
$this->lang['ARCreditNote'] = 'AR Credit Note';
$this->lang['realizationCode'] = 'Realization Code';
$this->lang['realizationAmount'] = 'Realization Amount';
$this->lang['salesmanPrivileges'] = 'Salesman Privileges';
$this->lang['allSalesman'] = 'All Salesman';
$this->lang['ARDiscountApproval'] = 'AR Discount Approval';
$this->lang['allServices'] = 'All Services';
$this->lang['submissionDate'] = 'Submission Date';
$this->lang['JOWODate'] = 'JO / WO Date';
$this->lang['cashOutRef'] = 'Cash Out Ref.';
$this->lang['shipmentManifestReport'] = 'Shipment Manifest Report';
$this->lang['journalDate'] = 'Journal Date';
        
$this->lang['invoicePeriod'] = 'Periode Invoice';
$this->lang['dateRecurring'] = 'Tgl Recurring';
$this->lang['postPaid'] = 'Pascabayar';
$this->lang['billingDate'] = 'Tanggal Penagihan';
$this->lang['salesOrderSubscriptionReport'] = 'Laporan Order Berlangganan';
$this->lang['installationWorkOrderReport'] = 'Laporan SPK Instalasi';
$this->lang['qtyAverage'] = 'Qty. Avg.';
$this->lang['avg'] = 'Avg.';

$this->lang['ticketSupportReport'] = 'Ticket Support Report';
$this->lang['ticketSupportWorkOrderReport'] = 'Work Order Support Report';
$this->lang['installationBASTReport'] = 'BAST Report';
$this->lang['invoiceOrderSubscriptionReport'] = 'Invoice (Subscription) Report';

$this->lang['timespanReport'] = 'Timespan Report';
$this->lang['interval'] = 'Interval';
$this->lang['needRealization'] = 'Meed Realization';
$this->lang['variantProduct'] = 'Variant Product';
$this->lang['arStatus'] = 'AR Status';
$this->lang['quizResult'] = 'Quiz Result';
$this->lang['correct'] = 'Correct';
$this->lang['incorrect'] = 'Incorrect';
$this->lang['variant'] = 'Variant';
$this->lang['Option'] = 'Option';
$this->lang['Tiering'] = 'Tiering'; 
$this->lang['correctAnswer'] = 'Correct Answer'; 
$this->lang['level'] = 'Level'; 
$this->lang['primary'] = 'Primary'; 
$this->lang['ARAPCashflowReport'] = 'AR/AP Cashflow Report'; 
$this->lang['ar/apPayment'] = 'AR/AP Payment'; 
$this->lang['icon'] = 'Icon'; 
$this->lang['minimum'] = 'Minimum';
$this->lang['lumpSum'] = 'Lump Sum';
$this->lang['start'] = 'Start';
$this->lang['break'] = 'Break';
$this->lang['end'] = 'End';
$this->lang['workingTime'] = 'Working Time';
$this->lang['workHour'] = 'Working Hour';
$this->lang['overTime'] = 'Over Time';
$this->lang['salesOrderWorkshop'] = 'Sales Order Workshop';
$this->lang['receiptValidation'] = 'Receipt Validation';
$this->lang['IDNumber'] = 'ID Number'; 
$this->lang['signature'] = 'Signature';
$this->lang['externalLink'] = 'External Link'; 
$this->lang['virtualAccount'] = 'Virtual Account'; 
$this->lang['sort'] = 'Sort'; 
$this->lang['asc'] = 'Asc'; 
$this->lang['desc'] = 'Desc';  
$this->lang['reset'] = 'Reset'; 
$this->lang['totalShopping'] = 'Item(s) Subtotal'; 
$this->lang['shoppingList'] = 'Shopping List';
$this->lang['continueToPayment'] = 'Continue to Payment';
$this->lang['voucherReport'] = 'Voucher Report';
$this->lang['customerName'] = 'Customer Name';
$this->lang['splitReimburse'] = 'Split Reimburse';
$this->lang['careerCategory'] = 'Career Category';
$this->lang['careerField'] = 'Career Field';
$this->lang['requirement'] = 'Requirement';
$this->lang['paymentInstruction'] = 'Payment instruction';
$this->lang['careers'] = 'Career';
$this->lang['dropOffLocation'] = 'Drop Off Location';
$this->lang['searchLocation'] = 'Search location'; 
$this->lang['apCommission'] = 'AP Commission'; 
$this->lang['apCommissionPayment'] = 'AP Commission Payment'; 
$this->lang['apCommissionReport'] = 'AP Commission Report'; 
$this->lang['apCommissionPaymentReport'] = 'AP Commission Payment Report'; 
$this->lang['recruitment'] = 'Recruitment'; 
$this->lang['galleryHumanResource'] = 'Gallery (HR)'; 
$this->lang['galleryCategory'] = 'Gallery Category';  
$this->lang['galleryHumanResourceCategory'] = 'Gallery (HR) Category';  
$this->lang['newsletterSubscription'] = 'Newsletter Subscription';  
$this->lang['newsletterSubscriptionReport'] = 'Newsletter Subscription Report';  
$this->lang['importir'] = 'Importir';  
$this->lang['goodsDescription'] = 'Goods Description';  
$this->lang['destuffingLocation'] = 'Destuffing Location';  
$this->lang['stuffingLocation'] = 'Stuffing Location';  
$this->lang['exportir'] = 'Exportir'; 
$this->lang['deadline'] = 'Deadline';    
$this->lang['salesOrderToInvoiceReport'] = 'Sales Order to Invoice Report';    
$this->lang['medicalRecord'] = 'Medical Record';
$this->lang['diff'] = 'Diff.';
$this->lang['video'] = 'Video'; 
$this->lang['showHistoryOf'] = 'Show History of';
$this->lang['transactionCashFlowReport'] = 'Cash Flow Transaction Report';
$this->lang['apPayment'] = 'AP Payment';
$this->lang['arPayment'] = 'AR Payment'; 
$this->lang['paymentGateway'] = 'Payment Gateway'; 
$this->lang['supplierDownpaymentSettlement'] = 'Supplier Downpayment Settlement';
$this->lang['customerDownpaymentSettlement'] = 'Customer Downpayment Settlement';
$this->lang['refund'] = 'Refund';
$this->lang['pointDeduction'] = 'Point Deduction';
$this->lang['pointHistory'] = 'Point History';
$this->lang['eligiblePoint'] = 'Eligible Point';
$this->lang['claimVoucher'] = 'Claim Voucher';
$this->lang['prepaidExpense'] = 'Prepaid Expense';
$this->lang['prepaidCost'] = 'Prepaid Cost';
$this->lang['costReconsiliation'] = 'Cost Reconsiliation';
$this->lang['reconsiliation'] = 'Rekonsiliasi';
$this->lang['invoiceDetail'] = 'Invoice Detail';
$this->lang['costAccount'] = 'Cost Account';
$this->lang['voucherCode'] = 'Voucher Code';
$this->lang['startingBalance'] = 'Starting Balance';
$this->lang['cashback'] = 'Cashback';
$this->lang['expDate'] = 'Exp. Date';
$this->lang['apiLogReport'] = 'API Log Report';
$this->lang['salesOrderSummaryReport'] = 'Sales Order Summary Report';
$this->lang['buyer'] = 'Buyer';
$this->lang['seller'] = 'Seller';
$this->lang['agent'] = 'Agent';
$this->lang['transactionValue'] = 'Transaction Value';
$this->lang['propertyInformation'] = 'Property Information';
$this->lang['bankProvision'] = 'Bank Provision';
$this->lang['bank'] = 'Bank'; 
$this->lang['adminRevenue'] = 'Admin Revenue';
$this->lang['commissionTransaction'] = 'Commission Transaction';
$this->lang['salesOrderSummary'] = 'Sales Order Summary';
$this->lang['salesOrderCategory'] = 'Sales Order Category';
$this->lang['property'] = 'Property';
$this->lang['activePeriod'] = 'Active Period';
$this->lang['memberID'] = 'Member ID';
$this->lang['meetingPoint'] = 'Meeting Point';
$this->lang['meetingSchedule'] = 'Meeting Schedule';
$this->lang['meeting'] = 'Meeting';
$this->lang['meetingLink'] = 'Meeting Link';

$this->lang['activationEmailContent'] = 'Dear {{CUSTOMER_NAME}},
									 <br>
									Thank you for creating an account, you\'re almost done! To complete your registration, click on the link below to verify your account and email address.
									<br><br> 
									{{ACTIVATION_LINK}}
									<br><br> 
									Best Regards,<br>
									{{COMPANY_NAME}}
								';
								
$this->lang['resetPasswordRequestEmailContent'] = 'Dear {{CUSTOMER_NAME}},
			 <br>
			 You or someone has used this email to reset password. Please click the following link to reset password.<br> 
			{{RESET_PASSWORD_LINK}}
			 <br><br> 
			Best Regards,<br>
			{{COMPANY_NAME}}';
	
			
$this->lang['resetPasswordContent'] =  '
					Dear {{CUSTOMER_NAME}},
					 <br>
					  Your password has been reset to <strong>{{NEW_PASSWORD}}</strong><br><br>
					Best Regards,<br>
					{{COMPANY_NAME}}
				';

$this->lang['paymentSuccessContent'] =  'Transaksi berhasil. Terima kasih telah melakukan transaksi di situs kami.';
$this->lang['paymentPendingContent'] =  'Terima kasih telah melakukan transaksi di situs kami.<br>Silahkan melakukan pembayaran sesuai dengan instruksi yang telah kami kirimkan ke email Anda. Terima kasih.';

?>