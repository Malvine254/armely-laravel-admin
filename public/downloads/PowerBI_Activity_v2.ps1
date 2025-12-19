# Login to the Data Gateway Service Account
# Login-PowerBIServiceAccount

$password = "Rac60722"  | ConvertTo-SecureString -asPlainText -Force
$username = "gerald.wiley@armely.com"
$credential = New-Object -TypeName System.Management.Automation.PSCredential -argumentlist $username, $password

Connect-PowerBIServiceAccount -Credential $credential

$startDateTime = Get-Date -Year (Get-Date).year -Month (Get-Date).month -Day (Get-Date).day -Hour 00 -Minute 0 -Second 0
$endDateTime =  Get-Date -Year (Get-Date).year -Month (Get-Date).month -Day (Get-Date).day -Hour 23 -Minute 59 -Second 59

# Specify the file path including the directory
$jsonFilePath = "D:\Scripts\PowerBI\Output\"
$jsonFileName ="activity_events.json"
$filenum = 1

# Specify the format for the datetime string
$dateFormat = "yyyy-MM-ddTHH:mm:ss"

# Create an empty array to store the activity events
$activityEvents = @()

# Display checkpoint
    $Precheckpoint = "Entering Loop"
    Write-Host $Precheckpoint

# Loop through two days
for ($i = 0; $i -lt 15; $i++) {
    $startDateTimeString = $startDateTime.ToString($dateFormat)
    $endDateTimeString = $endDateTime.ToString($dateFormat)
	
	#$filenum += 1
	
	$filenum = get-date $startDateTime -format "MM-dd-yyyy"
	
    # Display checkpoint
    $checkpoint = "Processing date: $startDateTimeString to: $endDateTimeString"
    Write-Host $checkpoint

    # Retrieve activity events and add them to the array
    $events = Get-PowerBIActivityEvent -StartDateTime $startDateTimeString -EndDateTime $endDateTimeString 
    # $activityEvents += $events
	$activityEvents = $events

$activityEvents | Out-File -FilePath "${jsonFilePath}${filenum}_${jsonFileName}" -Encoding UTF8

    $startDateTime = $startDateTime.AddDays(-1)
    $endDateTime = $endDateTime.AddDays(-1)
}

Get-ChildItem -Path $jsonFilePath -Recurse | ForEach-Object{

    #Check if file length if less than 10
    if ($_.Length / 1KB -lt 1){ Remove-Item "${jsonFilePath}$_" -Force}
	
}

$allReports = Get-PowerBIReport -Scope Organization
$jsonAllReports =  $allReports | ft DatasetId, Id, Name 
$jsonAllReports
$jsonAllReports | Out-File -FilePath "${jsonFilePath}AllReports.json" -Encoding UTF8


Write-Host "JSON file created: $jsonFilePath"
