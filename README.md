# EncryptedBookkeepingWebsite  
A simple test website to allow safely encrypted bookkeeping.  

# Features
Allow selected users to be a Bookkeeper (reffered as 'Booker' on the Website).  
Bookers are able to create personal notes and account outgoing and incoming money.  
Everything is completely encrypted with a individually created and only locally stored encryption key for maximum privacy.  

# Setting up Mysql:  
-- tablename --  
row1 | row2 | row3  
xxxx | xxxx | xxxx  

-- accounts --
userid | username | password | email | role | booker | telegram | joined | balance | deposited | deposits | tickets | orders | rememberToken |  
varchar(255) | varchar(255) | varchar(255) | varchar(255) | int | int | varchar(255) | timestamp | decimal | decimal | int | int | int | varchar(255)  

-- adminlog --  
date | admin | target | action | note  
timestamp | varchar(255) | varchar(255) | varchar(255) | varchar(255)  

-- books --  
refid | owner | date | shop | method | progress | todo | item | value | profit | ftid | email | note | name | address | payment  
varchar(255) | varchar(255) | date | varchar(255) | int | varchar(255) | decimal | decimal | decimal | varchar(255) | varchar(255) | varchar(255) | varchar(255) | varchar(255) | varchar(255)  

-- notes --  
owner | note |  
varchar(255) | text  

-- pastes --  
owner | pasteid | title | password | visibility | type | text | created  
varchar(255) | varchar(255) | varchar(255) | varchar(255) | varchar(255) | timestamp |  

-- serialkeys --  
skey | creationDate | claimDate | creator | claimer | booker  
varchar(255) | timestamp | date | varchar(255) | varchar(255) | int  
