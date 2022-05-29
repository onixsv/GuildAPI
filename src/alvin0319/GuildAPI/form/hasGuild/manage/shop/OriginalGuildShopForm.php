<?php

/*
 *
 *  /$$$$$$            /$$ /$$       /$$  /$$$$$$  /$$$$$$$  /$$$$$$
 * /$$__  $$          |__/| $$      | $$ /$$__  $$| $$__  $$|_  $$_/
 * | $$  \__/ /$$   /$$ /$$| $$  /$$$$$$$| $$  \ $$| $$  \ $$  | $$
 * | $$ /$$$$| $$  | $$| $$| $$ /$$__  $$| $$$$$$$$| $$$$$$$/  | $$
 * | $$|_  $$| $$  | $$| $$| $$| $$  | $$| $$__  $$| $$____/   | $$
 * | $$  \ $$| $$  | $$| $$| $$| $$  | $$| $$  | $$| $$        | $$
 * |  $$$$$$/|  $$$$$$/| $$| $$|  $$$$$$$| $$  | $$| $$       /$$$$$$
 *  \______/  \______/ |__/|__/ \_______/|__/  |__/|__/      |______/
 *
 * Copyright (C) 2020 alvin0319
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace alvin0319\GuildAPI\form\hasGuild\manage\shop;

use alvin0319\GuildAPI\event\EconomyEvent;
use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function count;
use function is_int;
use function is_string;

class OriginalGuildShopForm implements Form{

	/** @var Guild */
	protected $guild;

	protected $message = "";

	public function __construct(Guild $guild, string $message = ""){
		$this->guild = $guild;
		$this->message = $message;
	}

	public function jsonSerialize() : array{
		$pointCost = GuildAPI::getInstance()->getConfig()->getNested("guild-point-money", 10000);
		$perPoint = GuildAPI::getInstance()->getConfig()->getNested("guild-per-point", 100);
		$guildNameTagCost = GuildAPI::getInstance()->getConfig()->getNested("original-nametag-point", 1000);
		$guildAnnounceCost = GuildAPI::getInstance()->getConfig()->getNested("original-announce-point", 1000);
		$guildIncreaseSize = GuildAPI::getInstance()->getConfig()->getNested("original-increase-size", 1000);
		return [
			"type" => "form",
			"title" => "§l오리지널 길드 상점",
			"content" => "우리 길드 포인트는 {$this->guild->getGuildStorage()->getGuildPoints()} 포인트 입니다!\n\n길드 인원: " . count($this->guild->getMembers()) . "\n\n" . $this->message,
			"buttons" => [
				["text" => "나가기"],
				["text" => "§l길드 포인트 환전\n{$pointCost}원 필요 ({$perPoint} 포인트 환전)"],
				["text" => "§l길드 네임태그 활성화\n{$guildNameTagCost} 포인트 필요"],
				["text" => "§l길드 공지 활성화{$guildAnnounceCost} 포인트 필요"],
				["text" => "§l길드 인원 10명 확장하기\n{$guildIncreaseSize} 포인트 필요"]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			$pointCost = GuildAPI::getInstance()->getConfig()->getNested("guild-point-money", 10000);
			$perPoint = GuildAPI::getInstance()->getConfig()->getNested("guild-per-point", 100);
			$guildNameTagCost = GuildAPI::getInstance()->getConfig()->getNested("original-nametag-point", 1000);
			$guildAnnounceCost = GuildAPI::getInstance()->getConfig()->getNested("original-announce-point", 1000);
			$guildIncreaseSize = GuildAPI::getInstance()->getConfig()->getNested("original-increase-size", 1000);
			switch($data){
				case 1:
					$ev = new EconomyEvent($player, $pointCost);
					$ev->call();
					if(!$ev->isCancelled()){
						$this->guild->getGuildStorage()->setGuildPoints($this->guild->getGuildStorage()->getGuildPoints() + $perPoint);
						$player->sendForm(new OriginalGuildShopForm($this->guild, "§a길드 포인트 환전에 성공했습니다."));
					}else{
						$player->sendForm(new OriginalGuildShopForm($this->guild, "§c길드 포인트 환전에 필요한 돈이 부족합니다."));
					}
					break;
				case 2:
					if(!$this->guild->getGuildStorage()->getAllowNameTag()){
						if($this->guild->getGuildStorage()->getGuildPoints() >= $guildNameTagCost){
							$this->guild->getGuildStorage()->setGuildPoints($this->guild->getGuildStorage()->getGuildPoints() - $guildNameTagCost);
							$this->guild->getGuildStorage()->setAllowNameTag(true);
							$player->sendForm(new OriginalGuildShopForm($this->guild, "§a길드 네임태그 구매에 성공했습니다."));
						}else{
							$player->sendForm(new OriginalGuildShopForm($this->guild, "§c길드 네임태그 활성화에 필요한 길드 포인트가 부족합니다."));
						}
					}else{
						$player->sendForm(new OriginalGuildShopForm($this->guild, "§c이미 이 기능이 활성화 되어있습니다."));
					}
					break;
				case 3:
					if(!is_string($this->guild->getGuildStorage()->getAnnounce())){
						if($this->guild->getGuildStorage()->getGuildPoints() >= $guildAnnounceCost){
							$this->guild->getGuildStorage()->setGuildPoints($this->guild->getGuildStorage()->getGuildPoints() - $guildNameTagCost);
							$this->guild->getGuildStorage()->setAnnounce("");
							$player->sendForm(new OriginalGuildShopForm($this->guild, "§a길드 공지 구매에 성공했습니다."));
						}else{
							$player->sendForm(new OriginalGuildShopForm($this->guild, "§c길드 공지 활성화에 필요한 길드 포인트가 부족합니다."));
						}
					}else{
						$player->sendForm(new GuildAnnounceSetForm($this->guild));
					}
					break;
				case 4:
					if($this->guild->getGuildStorage()->getGuildPoints() >= $guildIncreaseSize){
						$this->guild->getGuildStorage()->setGuildPoints($this->guild->getGuildStorage()->getGuildPoints() - $guildIncreaseSize);
						$this->guild->getGuildStorage()->setGuildCount($this->guild->getGuildStorage()->getGuildCount() + 10);
						$player->sendForm(new OriginalGuildShopForm($this->guild, "§a길드 인원 확장에 성공했습니다."));
					}else{
						$player->sendForm(new OriginalGuildShopForm($this->guild, "§c길드 인원 확장에 필요한 길드 포인트가 부족합니다."));
					}
			}
		}
	}
}
