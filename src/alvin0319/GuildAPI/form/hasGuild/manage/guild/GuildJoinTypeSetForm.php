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

namespace alvin0319\GuildAPI\form\hasGuild\manage\guild;

use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_int;

class GuildJoinTypeSetForm implements Form{

	/** @var Guild */
	protected Guild $guild;

	public function __construct(Guild $guild){
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "form",
			"title" => "§l길드 가입방식 선택하기",
			"content" => "현재 우리 길드의 가입 방식은 {$this->guild->getGuildJoinType()} 입니다!",
			"buttons" => [
				["text" => "나가기"],
				["text" => "§l공개\n길드가 검색에서 노출되고, 누구나 가입 가능합니다."],
				["text" => "비공개\n길드가 검색에서 노출되지 않고, 가입이 불가능합니다."],
				["text" => "가입 신청\n길드가 검색에서 노출되지만, 가입신청을 넣어야 합니다."]
				//["text" => "초대 한정"]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			if($data !== 0){
				$options = [
					"",
					Guild::GUILD_TYPE_OPEN,
					Guild::GUILD_TYPE_CLOSED,
					Guild::GUILD_TYPE_REQUEST_JOIN
					//Guild::GUILD_TYPE_INVITATION
				];
				if(isset($options[$data])){
					$this->guild->setGuildJoinType($options[$data]);
					GuildAPI::message($player, "길드 가입 방식을 {$options[$data]}(으)로 변경했습니다.");
				}
			}
		}
	}
}
