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

use alvin0319\GuildAPI\Guild;
use alvin0319\GuildAPI\GuildAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_string;

class GuildAnnounceSetForm implements Form{

	/** @var Guild */
	protected Guild $guild;

	public function __construct(Guild $guild){
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "custom_form",
			"title" => "§l길드 공지 작성하기",
			"content" => [
				[
					"type" => "input",
					"text" => "길드 공지를 비활성화 시키려면 빈 칸으로 남겨주세요.",
					"default" => $this->guild->getGuildStorage()->getAnnounce()
				]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_string($data[0])){
			$this->guild->getGuildStorage()->setAnnounce($data[0]);
			GuildAPI::message($player, "길드 공지를 " . ($data[0] === "" ? "비활성화 " : "{$data[0]}§r§7으(로) 설정") . "했습니다.");
		}
	}
}
