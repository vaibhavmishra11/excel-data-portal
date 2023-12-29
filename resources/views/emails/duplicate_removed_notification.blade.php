<!DOCTYPE html>
<html>
<head>
    <title>Duplicate Clients Removed</title>
</head>
<body>
    <p>Hello,</p>
    
    <p>Your file has been processed successfully. Here is the import summary:</p>
    
    <ul>
        <li>Imported Count: {{ $importSummary['importedCount'] }}</li>
        <li>Duplicate Count: {{ $importSummary['duplicateCount'] }}</li>
        <li>Rejected Count: {{ $importSummary['rejectedCount'] }}</li>
    </ul>

    <p>Thank you for using our application.</p>
</body>
</html>
