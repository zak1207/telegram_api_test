#include "Telegram.au3"
#include <Array.au3>
#include <Process.au3>

;#pragma compile(Console, True)  ; Print ConsoleWrite to stdout

Local $ChatID = '123456789'
Local $Token = '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11'

If(($ChatID = '') or ($Token = '')) Then
    ConsoleWrite("Warning! ChatID or Token not specified!")
    Exit -1
EndIf

ConsoleWrite("Initializing bot... " & _InitBot($Token) & " ...Done!" & @CRLF)

ConsoleWrite("Who am I? Well..." & @CRLF)
Local $myData = _GetMe()
ConsoleWrite("Oh, yeah, my name is " & $myData[2] & ", you can find me at @" & $myData[1] & ". For developers, my Telegram ID is " & $myData[0] & ". That's it!" & @CRLF)

$StartTimer = TimerInit() ; Define the initial time we will be comparing to later
$process = "xmr-stak-cpu.exe" ; Define the process
$exe = "xmr-stak-cpu.exe" ; Define the executable

Checkfile($exe)
Checkprocess() ; Run our checkprocess() function on initial execute

While 1 ; Infinite Loop Condition is always true, you can exit these loops with "ExitLoop"
    If TimerDiff($StartTimer) > 60000 Then ; Only run the conditional code if the difference in time is greater than 1 min (60000 Miliseconds)
		Checkfile($exe)
        Checkprocess()
    EndIf
    Sleep(10) ; So we don't kill the CPU
WEnd ; End of While Loop

Func Checkfile($iFile)
	Local $iFileExists = FileExists($iFile)
	If Not $iFileExists Then
		ConsoleWrite("File " & $iFile & " on computer " & @ComputerName & " not found. Exit. " & @CRLF)
		$MsgID = _SendMsg($ChatID,"File " & $iFile & " on computer " & @ComputerName & " not found. Exit. " & @CRLF)
		ConsoleWrite($MsgID & @CRLF)
		Exit -1
	EndIf
EndFunc    ;==>Checkfile

Func Checkprocess()
	If FileExists('stop.txt') Then
		ConsoleWrite("The script is stopped." & " Computer: " & @ComputerName & @CRLF)
		$MsgID = _SendMsg($ChatID,"The script is stopped." & " Computer: " & @ComputerName & @CRLF)
		Exit 0
	EndIf
    If Not ProcessExists($process) Then
		Run($exe) ; checks if process exists.. If not, it will Run the process
		ProcessSetPriority($process, 0)
		WinWaitActive("[CLASS:ConsoleWindowClass]", "")
		Local $iPid = WinGetProcess("[CLASS:ConsoleWindowClass]", "")
		Local $sName = _ProcessGetName($iPid)
		ConsoleWrite("Running process name is: " & $sName & " Process ID: " & $iPid & " On computer: " & @ComputerName & @CRLF)
		$MsgID = _SendMsg($ChatID,"Running process name is: " & $sName & " Process ID: " & $iPid & " On computer: " & @ComputerName & @CRLF)
		ConsoleWrite($MsgID & @CRLF)
		$StartTimer = TimerInit() ; Reset the timer since the script just ran
	EndIf
EndFunc   ;==>Checkprocess
