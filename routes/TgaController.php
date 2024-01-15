<?php

namespace App\TireGroup_airwaybill;
use App\Common\View;
use App\TireGroup_airwaybill\TgaAPI;
use App\Common\Automail;
use App\Email\EmailAPI;
use App\Common\Datatables;

class TgaController {

	public function __construct() {
		$this->view = new View;
		$this->Tga = new TgaAPI;
		$this->automail = new Automail;
		$this->email = new EmailAPI;
		$this->datatables = new Datatables;
	}

	public function all($request, $response, $args) {
		return $this->view->render('pages/TireGroup_airwaybill/all');
	}

	public function getLogs($request, $response, $args) {
		try {
			$parsedBody = $request->getParsedBody();
			$data = $this->Tga->getLogs($this->datatables->filter($parsedBody));
			$pack = $this->datatables->get($data, $parsedBody);
			return $response->withJson($pack);
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
	}

	public function allSend($request, $response, $args) {
		try {
			$projectId = 13;
			$To = [];
			$ToCC = [];
			$time_set = [
				'A' => [
					'start_date' => date('Y-m-d', strtotime("-1 day")),
					'end_date' => date('Y-m-d'),
					'start_time' => '17:40:01',
					'end_time' => '23:59:59'
				],
				'B' => [
					'start_date' => date('Y-m-d'),
					'end_date' => date('Y-m-d'),
					'start_time' => '00:00:01',
					'end_time' => '12:00:00'
				],
				'C' => [
					'start_date' => date('Y-m-d'),
					'end_date' => date('Y-m-d'),
					'start_time' => '12:00:01',
					'end_time' => '16:00:00'
				],
				'D' => [
					'start_date' => date('Y-m-d'),
					'end_date' => date('Y-m-d'),
					'start_time' => '16:00:01',
					'end_time' => '17:40:00'
				],
				'X' => [
					'start_date' => date('Y-m-d'),
					'end_date' => date('Y-m-d'),
					'start_time' => '00:00:01',
					'end_time' => '17:40:00'
				]
			];
			$argv[1] = 'B';
			// echo '<pre>';
			// print_r($time_set);
			// echo '</pre>';
			//  exit();
			if (!isset($argv[1])) {
				exit('no parameter!!');
			}
			if (!array_key_exists($argv[1], $time_set)) {
				exit('no key!!');
			}
			$vessel_mail = $this->Tga->getAirWayBillData($time_set[$argv[1]]);

			foreach ($vessel_mail as $v) {
				$checktypetosendMail = $this->Tgv->getMail( $v['CONDITION']);
				$emailgroup = $this->automail->getCustomerMail($v['CUSTACCOUNT']);
				$sender_ = $this->Tgv->getSender($v['CUSTACCOUNT']);

					foreach ($checktypetosendMail as $z ) {
						if($z['EmailType'] == 1) {
							//array_push($To,[$z['Email']]);
							array_push($To,'worawut_s@deestone.com');
						}
						if($z['EmailType'] == 2) {
							array_push($ToCC,$z['Email']);
						}
					}

					$bodytest =	$this->Tgv->getVesselBody(
						$v['CUSTNAME'],
						$v['QUOTATIONID'],
						$v['CUSTOMERREF'],
						$v['TOPORT'],
						date('d/m/Y', strtotime( str_replace('/', '-', $v['BEFORE_ETD']) )),
						date('d/m/Y', strtotime($v['AFTER_ETD'])),
						date('d/m/Y', strtotime( str_replace('/', '-', $v['BEFORE_ETA']) )),
						date('d/m/Y', strtotime($v['AFTER_ETA'])),
						$v['INVNO'],
						$v['DSG_SALESID'],
						$v['BEFORE_VESSEL'],
						$v['BEFORE_FEEDER'],
						$v['AFTER_VESSEL'],
						$v['AFTER_FEEDER']
					);

					$mail = $this->email->sendEmail(
						$this->Tgv->getVesselSubject($v['QUOTATIONID'], $v['INVNO'], $v['CUSTNAME']),
						$bodytest,
						$To,
						[],
						[],
						[],
						"",
						$sender_
					);

					if ($mail['result'] === true) {
						$this->email->sendEmail(
							$this->Tgv->getVesselSubject($v['QUOTATIONID'], $v['INVNO'], $v['CUSTNAME']),
							$bodytest,
							$emailgroup['sender'],
							[],
							[],
							[],
							"",
							$sender_
						);
						$this->automail->logging(
							$projectId,
							'Message has been sent',
							$v['CUSTACCOUNT'],
							$v['DSG_SALESID'],
							null,
							$v['QUOTATIONID'],
							null,
							null,
							'Ax'
						);
						$this->Tgv-logmailvessel('vessel', $v['INVNO'] . ' | ' . $v['CUSTNAME'] . ' | ' . $v['CUSTACCOUNT'] . ' | ' . $mail['message'] );
					}
			}

		} catch (\Exception $e) {
			echo $e->getMessage();
		}

	}




}
