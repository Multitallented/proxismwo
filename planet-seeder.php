<?php include 'header.php'; ?>
<?php
$conn = getConnection();
/*$sql = "SELECT planet_name, region FROM planet;";
$result = mysqli_query($conn, $sql);
while($row = $result->fetch_row()) {*/
		$new_production = "";
		$cons = "";
		//$region = $row[1];
		$region = mysqli_real_escape_string($conn, $_GET['r']);
		echo $region . "<br>";
		//Clan
		if ($region == 'clan') {
			$new_production = "All, dropship16, dropship28, dropship40";
			$sql = "UPDATE planet SET production='" . $new_production . "', match_conditions='" . 
					$cons . "' WHERE planet_name='" . $row[0] . "';";
			$result1 = mysqli_query($conn, $sql);
			mysqli_free_result($result1);
			//continue;
			die();
		}
			
		//Common mechs: HBK, JR7, CPLT, AS7, CN9, LCT, BJ, SHD, QKD, TDR, ON1, BLR, STK, HGN
		$rand = rand(1, 15);
		if ($rand == 2) {
			$new_production .= ", HBK-4G, HBK-4H, HBK-4J, HBK-4P, HBK-4SP";
		} elseif ($rand == 3) {
			$new_production .= ", JR7-D, JR7-F, JR7-K";
		} elseif ($rand == 4) {
			$new_production .= ", CPLT-C1, CPLT-C4, CPLT-A1, CPLT-K2";
		} elseif ($rand == 5) {
			$new_production .= ", AS7-D, AS7-D-DC, AS7-K, AS7-RS, AS7-BH";
		} elseif ($rand == 6) {
			$new_production .= ", CN9-A, CN9-D, CN9-AL, CN9-YLW";
		} elseif ($rand == 7) {
			$new_production .= ", LCT-1V, LCT-3M, LCT-3S";
		} elseif ($rand == 8) {
			$new_production .= ", BJ-1, BJ-1DC, BJ-1X, BJ-3";
		} elseif ($rand == 9) {
			$new_production .= ", SHD-2D2, SHD-2H, SHD-5M";
		} elseif ($rand == 10) {
			$new_production .= ", QKD-4H, QKD-4G, QKD-5K";
		} elseif ($rand == 11) {
			$new_production .= ", TDR-5S, TDR-5SS, TDR-9SE";
		} elseif ($rand == 12) {
			$new_production .= ", ON1-PR, ON1-K, ON1-M, ON1-V, ON1-VA";
		} elseif ($rand == 13) {
			$new_production .= ", BLR-1D, BLR-1G, BLR-1S";
		} elseif ($rand == 14) {
			$new_production .= ", STK-M, STK-4N, STK-3H, STK-3F, STK-5M, STK-5S";
		} elseif ($rand == 15) {
			$new_production .= ", HGN-732, HGN-733, HGN-733C, HGN-733P, HGN-HM";
		}
		$new_rand = rand(1, 15);
		if ($rand != $new_rand) {
			if ($new_rand == 2) {
				$new_production .= ", HBK-4G, HBK-4H, HBK-4J, HBK-4P, HBK-4SP";
			} elseif ($new_rand == 3) {
				$new_production .= ", JR7-D, JR7-F, JR7-K";
			} elseif ($new_rand == 4) {
				$new_production .= ", CPLT-C1, CPLT-C4, CPLT-A1, CPLT-K2";
			} elseif ($new_rand == 5) {
				$new_production .= ", AS7-D, AS7-D-DC, AS7-K, AS7-RS, AS7-BH";
			} elseif ($new_rand == 6) {
				$new_production .= ", CN9-A, CN9-D, CN9-AL, CN9-YLW";
			} elseif ($new_rand == 7) {
				$new_production .= ", LCT-1V, LCT-3M, LCT-3S";
			} elseif ($new_rand == 8) {
				$new_production .= ", BJ-1, BJ-1DC, BJ-1X, BJ-3";
			} elseif ($new_rand == 9) {
				$new_production .= ", SHD-2D2, SHD-2H, SHD-5M";
			} elseif ($new_rand == 10) {
				$new_production .= ", QKD-4H, QKD-4G, QKD-5K";
			} elseif ($new_rand == 11) {
				$new_production .= ", TDR-5S, TDR-5SS, TDR-9SE";
			} elseif ($new_rand == 12) {
				$new_production .= ", ON1-PR, ON1-K, ON1-M, ON1-V, ON1-VA";
			} elseif ($new_rand == 13) {
				$new_production .= ", BLR-1D, BLR-1G, BLR-1S";
			} elseif ($new_rand == 14) {
				$new_production .= ", STK-M, STK-4N, STK-3H, STK-3F, STK-5M, STK-5S";
			} elseif ($new_rand == 15) {
				$new_production .= ", HGN-732, HGN-733, HGN-733C, HGN-733P, HGN-HM";
			}
		}
		$new_rand1 = rand(1, 15);
		if ($rand != $new_rand1 && $new_rand1 != $new_rand) {
			$new_rand = $new_rand1;
			if ($new_rand == 2) {
				$new_production .= ", HBK-4G, HBK-4H, HBK-4J, HBK-4P, HBK-4SP";
			} elseif ($new_rand == 3) {
				$new_production .= ", JR7-D, JR7-F, JR7-K";
			} elseif ($new_rand == 4) {
				$new_production .= ", CPLT-C1, CPLT-C4, CPLT-A1, CPLT-K2";
			} elseif ($new_rand == 5) {
				$new_production .= ", AS7-D, AS7-D-DC, AS7-K, AS7-RS, AS7-BH";
			} elseif ($new_rand == 6) {
				$new_production .= ", CN9-A, CN9-D, CN9-AL, CN9-YLW";
			} elseif ($new_rand == 7) {
				$new_production .= ", LCT-1V, LCT-3M, LCT-3S";
			} elseif ($new_rand == 8) {
				$new_production .= ", BJ-1, BJ-1DC, BJ-1X, BJ-3";
			} elseif ($new_rand == 9) {
				$new_production .= ", SHD-2D2, SHD-2H, SHD-5M";
			} elseif ($new_rand == 10) {
				$new_production .= ", QKD-4H, QKD-4G, QKD-5K";
			} elseif ($new_rand == 11) {
				$new_production .= ", TDR-5S, TDR-5SS, TDR-9SE";
			} elseif ($new_rand == 12) {
				$new_production .= ", ON1-PR, ON1-K, ON1-M, ON1-V, ON1-VA";
			} elseif ($new_rand == 13) {
				$new_production .= ", BLR-1D, BLR-1G, BLR-1S";
			} elseif ($new_rand == 14) {
				$new_production .= ", STK-M, STK-4N, STK-3H, STK-3F, STK-5M, STK-5S";
			} elseif ($new_rand == 15) {
				$new_production .= ", HGN-732, HGN-733, HGN-733C, HGN-733P, HGN-HM";
			}
		}
		
		//Marik CDA TBT AWS
		if ($region == 'marik') {
			$rand = rand(1,5);
			if ($rand == 2) {
				$new_production .= ", CDA-X5, CDA-2A, CDA-2B, CDA-3C, CDA-3M";
			} elseif ($rand == 3) {
				$new_production .= ", TBT-3C, TBT-5J, TBT-5N, TBT-7M, TBT-7K";
			} elseif ($rand == 4) {
				$new_production .= ", AWS-PB, AWS-8Q, AWS-8T, AWS-8V, AWS-9M, AWS-8R";
			} elseif ($rand == 5) {
				$new_production .= ", SDR-5D, SDR-5V, SDR-5K";
			}
		}
		//Davion CDA, KTO, JM6, VTR
		if ($region == 'davion') {
			$rand = rand(1,6);
			if ($rand == 2) {
				$new_production .= ", CDA-X5, CDA-2A, CDA-2B, CDA-3C, CDA-3M";
			} elseif ($rand == 3) {
				$new_production .= ", KTO-18, KTO-19, KTO-BOY, KTO-20";
			} elseif ($rand == 4) {
				$new_production .= ", JM6-FB, JM6-A, JM6-DD, JM6-S";
			} elseif ($rand == 5) {
				$new_production .= ", VTR-DS, VTR-9S, VTR-9K, VTR-9B";
			} elseif ($rand == 6) {
				$new_production .= ", CTF-IM, CTF-3D, CTF-1X, CTF-2X, CTF-4X";
			}
		}
		
		//Lyran CDA, KTO, COM, VTR
		if ($region == 'lyran') {
			$rand = rand(1,5);
			if ($rand == 2) {
				$new_production .= ", CDA-X5, CDA-2A, CDA-2B, CDA-3C, CDA-3M";
			} elseif ($rand == 3) {
				$new_production .= ", KTO-18, KTO-19, KTO-BOY, KTO-20";
			} elseif ($rand == 4) {
				$new_production .= ", COM-DK, COM-1D, COM-1B, COM-3A, COM-2D";
			} elseif ($rand == 5) {
				$new_production .= ", VTR-DS, VTR-9S, VTR-9K, VTR-9B";
			}
		}
		
		//Liao RVN JM6, CTF
		if ($region == 'liao') {
			$rand = rand(1,4);
			if ($rand == 2) {
				$new_production .= ", RVN-3L, RVN-2X, RVN-4X";
			} elseif ($rand == 3) {
				$new_production .= ", JM6-FB, JM6-A, JM6-DD, JM6-S";
			} elseif ($rand == 4) {
				$new_production .= ", CTF-IM, CTF-3D, CTF-1X, CTF-2X, CTF-4X";
			}
		}
		
		if ($region == 'kurita' || $region == 'frr') {
			$rand = rand(1,4);
			if ($rand == 2) {
				$new_production .= ", RVN-3L, RVN-2X, RVN-4X";
			} elseif ($rand == 3) {
				$new_production .= ", KTO-18, KTO-19, KTO-BOY, KTO-20";
			} elseif ($rand == 4) {
				$new_production .= ", DGN-FA, DGN-FL, DGN-1C, DGN-1N, DGN-5N";
			}
		}
		
		//Dropships
		if ($region != 'admin') {
			$rand = rand(15, 40);
			if ($rand > 39) {
				$new_production .= ", dropship16, dropship28, dropship40";
			} elseif ($rand > 36) {
				$new_production .= ", dropship16, dropship28";
			} elseif ($rand > 30) {
				$new_production .= ", dropship16";
			}
		}
		
		$new_production = substr($new_production, 2);
		
		//Match Conditions
		$rand = rand(1, 40);
		if ($rand == 11 || $rand == 10) {
			$cons = "No Jump Jets";
		} elseif ($rand == 12) {
			$cons = "No ECM";
		} elseif ($rand == 13) {
			$cons = "No PPCs";
		} elseif ($rand == 14) {
			$cons = "No consumable barrage modules";
		} elseif ($rand == 15) {
			$cons = "No Streak SRM2s";
		} elseif ($rand == 16) {
			$cons = "No AMS";
		} elseif ($rand == 17) {
			$cons = "No Assault mechs";
		} elseif ($rand == 18) {
			$cons = "No Light mechs";
		} elseif ($rand == 19) {
			$cons = "Each team must bring 3 mechs of each weight class";
		} elseif ($rand == 20) {
			$cons = "Max total tonnage of 750 per team";
		} elseif ($rand == 21) {
			$cons = "Max total tonnage of 650 per team";
		} elseif ($rand == 22) {
			$cons = "Max total tonnage of 550 per team";
		}

		echo $new_production;
		
		/*$capacity = 13 + rand(1, 19);
		
		$sql = "UPDATE planet SET capacity=" . $capacity . " WHERE planet_name='" . $row[0] . "';";
		$result1 = mysqli_query($conn, $sql);
		mysqli_free_result($result1);
}
mysqli_free_result($result);*/
?>