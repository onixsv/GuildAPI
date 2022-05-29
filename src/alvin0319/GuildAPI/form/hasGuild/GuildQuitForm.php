<?php
declare(strict_types=1);

namespace alvin0319\GuildAPI\form\hasGuild;

use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_bool;

class GuildQuitForm implements Form{

	/** @var Player */
	protected Player $player;

	/** @var Guild */
	protected Guild $guild;

	public function __construct(Player $player, Guild $guild){
		$this->player = $player;
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "modal",
			"title" => "§l" . $this->guild->getName() . " 길드 탈퇴하기",
			"content" => "정말 " . $this->guild->getName() . " 에서 탈퇴하시겠습니까?",
			"button1" => "§l네",
			"button2" => "§l아니요"
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_bool($data)){
			if($data){
				$this->guild->removeMember($player);
				GuildAPI::message($player, "길드에서 탈퇴했습니다.");
			}
		}
	}
}
