## GuildAPI

A PocketMine-MP Plugin that implements GuildSystem in [PocketMine-MP](https://github.com/pmmp/PocketMine-MP)

## Description

아직 미완임

## Plan...

|명령어|설명|
|---|---|
|/길드|길드 명령어
|/길드관리|길드관리 명령어(for op)|

|퍼미션|대상|설명|
|---|---|---|
|guildapi.*|true|guildapi 메인 퍼미션|
|guildapi.command|true|guildapi 명령어 퍼미션|
|guildapi.command.ui|true|길드 UI 오픈|
|guildapi.command.manage|op|오피가 손쉽게 모든 길드 관리|

API
-------

```php
$api = \alvin0319\GuildAPI\GuildAPI::getInstance(); // GuildAPI 인스턴스 반환

$api->addGuild(\pocketmine\Player, name); // 길드 생성
$api->removeGuild(\alvin0319\GuildAPI\Guild); // 길드 제거
$api->addMember(\pocketmine\Player|string, role, invitation); // 길드에 멤버를 추가
$api->removeMember(\pocketmine\Player|string, force); // 길드에서 멤버를 추방

$api->getGuild(name); // 길드 객체나 null 반환
$api->getGuildByPlayer(\pocketmine\Player); // 길드 객체나 null 반환

$guild = $api->getGuild("테스트");

$guild->getMembers(); // 길드 멤버들을 name => role 형태로 반환
$guild->getLeader(); // 길드의 리더를 반환
$guild->getOnlineMembers(); // 길드의 온라인 멤버들을 반환
$guild->getGuildJoinType(); // 길드 가입 형식을 반환
$guild->getOfficers(); // 길드의 공동리더들을 반환
$guild->setGuildJoinType(string); // 길드의 가입 방식을 설정
$guild->setRole(\pocketmine\Player|string, role); // 길드 멤버의 등급 설정


$storage = $guild->getGuildStorage(); // 길드 스토리지 반환
$storage->getGuildPoints(); // 길드 포인트 반환
$storage->getAllowNameTag(); // 길드 네임태그 활성화 여부 반환
$storage->getAnnounce(); // 길드 공지 반환, 공지가 있을 경우 string, 없을경우 false 반환
$storage->getGuildInvitations(); // 길드 가입신청 목록 반환
$storage->hasInvitation(\pocketmine\Player|string); // 길드 가입신청이 있는지 반환
$storage->setAllowNameTag(bool); // 길드의 네임태그를 활성화
$storage->setAnnounce(bool|string); // 길드의 공지를 활성화 (string은 활성화, false는 비활성화)
$storage->setGuildCount(int); // 길드 인원 설정
/** 
 * @internal
 * @throws Exception 
 */
$storage->setGuild(\alvin0319\GuildAPI\Guild); // 길드를 설정
```