CREATE TABLE tx_shortcutlink_domain_model_shortlink (
    shortlink varchar(32) DEFAULT '' NOT NULL,
    checksum varchar(64) DEFAULT '' NOT NULL,
    redirectto varchar(512) DEFAULT '' NOT NULL,
    feuser int DEFAULT '0' NOT NULL,
    index checksumidx (checksum),
    index shortlinkidx (shortlink)
);
