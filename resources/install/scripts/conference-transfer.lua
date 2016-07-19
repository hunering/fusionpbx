
max_tries = 3;
digit_timeout = 5000;
max_retries = 3;
tries = 0;
digit_min_length = 8;
digit_max_length = 11;
--define the trim function
	require "resources.functions.trim";

--define the explode function
	require "resources.functions.explode";

conference_name = argv[1];
dialplan_context = argv[2];

freeswitch.consoleLog("error", "conference_name:"..conference_name..";dialplan context"..dialplan_context.."\n");

if (not conference_name or not dialplan_context) then
	freeswitch.consoleLog("error", "Missing the conference name or dialplan_context name when invite user to a meeting\n");
	return;
end

	
session:setAutoHangup(false);

if ( session:ready() ) then

	while true do
		if ( session:answered() ) then	
			break;
		else
			if (session:ready()) then
				session:sleep(500);
				freeswitch.consoleLog("notice", "we are waiting for the answer*****\n");
			else 
				freeswitch.consoleLog("notice", "the call is killed*****\n");
				break;
			end
		end
	end
	if ( session:answered() ) then	
		session:answer( );
		pin_number = session:getVariable("pin_number");
		sounds_dir = session:getVariable("sounds_dir");

		--set the sounds path for the language, dialect and voice
		sounds_dir = session:getVariable("sounds_dir");
		default_language = session:getVariable("default_language");
		default_dialect = session:getVariable("default_dialect");
		default_voice = session:getVariable("default_voice");
		context = session:getVariable("context");
		if (not default_language) then default_language = 'en'; end
		if (not default_dialect) then default_dialect = 'us'; end
		if (not default_voice) then default_voice = 'callie'; end

		session:streamFile(sounds_dir.."/"..default_language.."/"..default_dialect.."/"..default_voice.."/conference/conf-bad-pin.wav");
		session:transfer("conf-room--"..conference_name, "XML", dialplan_context);
	else 
		
	end
end