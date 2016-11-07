<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Messenger/messenger_manage_report.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print __($guid, "You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Get action with highest precendence
	$highestAction=getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
	if ($highestAction==FALSE) {
		print "<div class='error'>" ;
		print __($guid, "The highest grouped action cannot be determined.") ;
		print "</div>" ;
	}
	else {
		$gibbonMessengerID=NULL ;
		if (isset($_GET["gibbonMessengerID"])) {
			$gibbonMessengerID=$_GET["gibbonMessengerID"] ;
		}
		$search=NULL ;
		if (isset($_GET["search"])) {
			$search=$_GET["search"] ;
		}


		print "<div class='trail'>" ;
		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/messenger_manage.php&search=$search'>" . __($guid, 'Manage Messages') . "</a> > </div><div class='trailEnd'>" . __($guid, 'View Send Report') . "</div>" ;
		print "</div>" ;

		if (!is_null($gibbonMessengerID)) {
	        echo '<h2>';
	        echo __($guid, 'Report Data');
	        echo '</h2>';

	        try {
	            $data = array('gibbonMessengerID' => $gibbonMessengerID);
	            $sql = "SELECT surname, preferredName, gibbonPerson.gibbonPersonID, gibbonMessengerReceipt.* FROM gibbonMessengerReceipt JOIN gibbonPerson ON (gibbonMessengerReceipt.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonMessengerID=:gibbonMessengerID ORDER BY surname, preferredName, contactType";
	            $result = $connection2->prepare($sql);
	            $result->execute($data);
	        } catch (PDOException $e) {
	            echo "<div class='error'>".$e->getMessage().'</div>';
	        }

	        echo "<table cellspacing='0' style='width: 100%'>";
	        echo "<tr class='head'>";
	        echo '<th>';

	        echo '</th>';
	        echo '<th>';
	        echo __($guid, 'Recipient');
	        echo '</th>';
	        echo '<th>';
	        echo __($guid, 'Contact Type');
	        echo '</th>';
	        echo '<th>';
	        echo __($guid, 'Contact Detail');
	        echo '</th>';
	        echo '<th>';
	        echo __($guid, 'Receipt Confirmed');
	        echo '</th>';
	        echo '</tr>';

	        $count = 0;
	        $rowNum = 'odd';
	        while ($row = $result->fetch()) {
                if ($count % 2 == 0) {
                    $rowNum = 'even';
                } else {
                    $rowNum = 'odd';
                }
                ++$count;

                //COLOR ROW BY STATUS!
                echo "<tr class=$rowNum>";
                echo '<td>';
                echo $count;
                echo '</td>';
                echo '<td>';
                echo formatName('', htmlPrep($row['preferredName']), htmlPrep($row['surname']), 'Student', true);
                echo '</td>';
                echo '<td>';
                echo $row['contactType'];
                echo '</td>';
                echo '<td>';
                echo $row['contactDetail'];
                echo '</td>';
                echo '<td>';
                if (is_null($row['key'])) {
					echo __($guid, 'N/A');
				}
				else {
					if ($row['confirmed'] == 'Y')
						echo "<img src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/iconTick.png'/> ";
					else
						echo "<img src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/iconCross.png'/> ";
				}
                echo '</td>';
                echo '</tr>';
            }
	        if ($count == 0) {
	            echo "<tr class=$rowNum>";
	            echo '<td colspan=5>';
	            echo __($guid, 'There are no records to display.');
	            echo '</td>';
	            echo '</tr>';
	        }
	        echo '</table>';
		}
	}
}
?>