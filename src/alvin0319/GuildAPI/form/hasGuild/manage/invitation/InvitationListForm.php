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

namespace alvin0319\GuildAPI\form\hasGuild\manage\invitation;

use alvin0319\GuildAPI\Guild;
use pocketmine\form\Form;
use pocketmine\player\Player;

class InvitationListForm implements Form{

	/** @var Guild */
	protected Guild $guild;

	protected array $invitations = [];

	public function __construct(Guild $guild){
		$this->guild = $guild;
	}

	public function jsonSerialize() : array{
		$arr = [];
		foreach($this->guild->getGuildStorage()->getGuildInvitations() as $name => $time){
			$this->invitations[] = $name;
			$arr[] = ["text" => "§l{$name}님\n신청한 날짜: {$time}"];
		}
		return [
			"type" => "form",
			"title" => "§l길드 가입신청 목록",
			"content" => "길드 가입신청을 선택해주세요!",
			"buttons" => $arr
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			if(isset($this->invitations[$data])){
				$player->sendForm(new InvitationInfoForm($this->guild, $this->invitations[$data]));
			}
		}
	}
}
