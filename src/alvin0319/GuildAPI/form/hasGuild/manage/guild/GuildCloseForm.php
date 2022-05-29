<?php
declare(strict_types=1);

namespace alvin0319\GuildAPI\form\hasGuild\manage\guild;

use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_bool;

class GuildCloseForm implements Form{

	/** @var Guild */
	protected Guild $guild;

	public function __construct(Guild $guild){
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "modal",
			"title" => "길드 폐쇄하기",
			"content" => "정말 " . $this->guild->getName() . " 길드를 폐쇄하시겠습니까?\n\n§c§l주의!! 길드 폐쇄시 포인트와 길드 생성 비용은 복구되지 않습니다,",
			"button1" => "네",
			"button2" => "아니요"
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_bool($data)){
			if($data){
				GuildAPI::getInstance()->removeGuild($this->guild);
				GuildAPI::message($player, "길드를 폐쇄했습니다.");
			}
		}
	}
}
