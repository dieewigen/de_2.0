#BR START
UPDATE de_user_data SET 
tick=tick+2500000,
sm_rboost=0,
restyp01=restyp01+9000000000,
restyp02=restyp02+4500000000,
restyp03=restyp03+1000000000,
restyp04=restyp04+500000000,
restyp05=restyp05+100000,
col=col+10000
WHERE npc=0 OR npc=2 AND sector > 1;

UPDATE de_system SET wt=wt+2500000, kt=kt+2500000;