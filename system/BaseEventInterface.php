<?php
/**
 * DZCP - deV!L`z ClanPortal - Server ( api.dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * geändert durch my-STARMEDIA und Codedesigns.
 *
 * DZCP - deV!L`z ClanPortal - Server
 * Homepage: https://www.dzcp.de
 * E-Mail: lbrucksch@hammermaps.de
 * Author Lucas Brucksch
 * Copyright 2021 © Codedesigns
 */

interface BaseEventInterface
{
    const JSON = 0;
    const JSONP = 1;
    const GZIP = 2;

    public function __construct(BaseSystem $baseSystem);

    public function __output(): void;

    public function __run(): void;

    public function __shutdown(): void;

    public function getBaseSystem(): BaseSystem;

    public function setContent(array $content): void;

    public function getContent(): array;

    public function setContentType(int $content_type): void;

    public function getContentType(): int;

    public function getEventDir(): string;

    public function getEventCall(): string;

    public function getEventLibDir(): string;

    public function getEventStoreDir(): string;

    public function getEventLogsDir(): string;

    public function isRedirect(): bool;

    public function useCert(bool $cert = false): void;

    public function getCert(): string;

    public function isReload(): bool;

    public function getEventCacheTime(): int;

    public function setEventCacheTime(int $event_cache_time): void;
}