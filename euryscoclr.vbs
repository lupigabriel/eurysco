On Error Resume Next
Set fso = CreateObject("Scripting.FileSystemObject")
If fso.FileExists(".\php\php.ini") Then
	Set f = fso.OpenTextFile(".\php\php.ini")
	Do Until f.AtEndOfStream
		Line = f.ReadLine
		If InStr(Line, ";extension=") Then
			fso.DeleteFile ".\php\ext\" & Split(Replace(Line, ";extension=", ""), " ")(0)
		End If
	Loop
	f.Close
End If
If fso.FileExists(".\apache\conf\httpd_eurysco_core.conf") Then
	Set f = fso.OpenTextFile(".\apache\conf\httpd_eurysco_core.conf")
	Do Until f.AtEndOfStream
		Line = f.ReadLine
		If InStr(Line, "#LoadModule ") Then
			fso.DeleteFile ".\apache\" & Split(Replace(Line, "#LoadModule ", ""), " ")(1)
		End If
	Loop
	f.Close
End If
Set fso = Nothing
Set f = Nothing