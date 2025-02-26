$folderPath = "." # Current directory, change this to your desired path
$excludedDirs = @("vendor", "node_modules")
$changedFiles = @()

Write-Host "Monitoring for file changes in: $folderPath"
Write-Host "Excluded directories: $($excludedDirs -join ', ')"
Write-Host "Press Ctrl+C to stop monitoring`n"

while ($true) {
    try {
        # Get initial state
        $files = Get-ChildItem -Path $folderPath -Recurse -File | 
            Where-Object {
                $path = $_.FullName
                $excluded = $false
                foreach ($dir in $excludedDirs) {
                    if ($path -match "\\$dir\\") {
                        $excluded = $true
                        break
                    }
                }
                -not $excluded
            }
        
        $initialState = $files | Select-Object Name, LastWriteTime, Length

        while ($true) {
            Start-Sleep -Seconds 1
            
            # Get current state
            $currentState = Get-ChildItem -Path $folderPath -Recurse -File | 
                Where-Object {
                    $path = $_.FullName
                    $excluded = $false
                    foreach ($dir in $excludedDirs) {
                        if ($path -match "\\$dir\\") {
                            $excluded = $true
                            break
                        }
                    }
                    -not $excluded
                } | 
                Select-Object Name, LastWriteTime, Length

            # Compare states
            $changes = Compare-Object -ReferenceObject $initialState -DifferenceObject $currentState -Property Name, LastWriteTime, Length

            # If changes detected
            if ($changes) {
                $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
                
                # Add new changes to array
                foreach ($change in $changes) {
                    $changedFiles += $change.Name
                }
                
                # Remove duplicates and sort
                $uniqueFiles = $changedFiles | Select-Object -Unique | Sort-Object
                
                # Clear screen and show header
                Clear-Host
                Write-Host "File Change Monitor - Last Updated: $timestamp`n"
                Write-Host "Changed Files (Total: $($uniqueFiles.Count)):`n"
                
                # List all unique changed files
                $uniqueFiles | ForEach-Object {
                    Write-Host "- $_"
                }
            }

            # Update initial state
            $initialState = $currentState
        }
    }
    catch {
        Write-Host "Error: $_"
        Start-Sleep -Seconds 2
        continue
    }
}