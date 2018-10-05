<?php
$data =  <<<json
	[
		{
			"topic_descr": "Business",
			"topic_id": 1,
			"fid":66
		},
		{
			"topic_descr": "Biology",
			"topic_id": 12,
			"fid":53			
		},
		{
			"topic_descr": "Geography",
			"topic_id": 32,
			"fid":55						
		},
		{
			"topic_descr": "Geology",
			"topic_id": 38,
			"fid":56						
		},
		{
			"topic_descr": "Housekeeping, leisure",
			"topic_id": 41,
			"fid":70
		},
		{
			"topic_descr": "Art",
			"topic_id": 57,
			"fid":64
		},
		{
			"topic_descr": "History",
			"topic_id": 64,
			"fid":60
		},
		{
			"topic_descr": "Computers",
			"topic_id": 69,
			"fid":52
		},
		{
			"topic_descr": "Literature",
			"topic_id": 102,
			"fid":61
		},
		{
			"topic_descr": "Mathematics",
			"topic_id": 113,
			"fid":49
		},
		{
			"topic_descr": "Medicine",
			"topic_id": 147,
			"fid":54
		},
		{
			"topic_descr": "Science (General)",
			"topic_id": 178,
			"fid":58
		},
		{
			"topic_descr": "Education",
			"topic_id": 183,
			"fid":63
		},
		{
			"topic_descr": "Other Social Sciences",
			"topic_id": 189,
			"fid":71
		},
		{
			"topic_descr": "Psychology",
			"topic_id": 198,
			"fid":59
		},
		{
			"topic_descr": "Religion",
			"topic_id": 205,
			"fid":65
		},
		{
			"topic_descr": "Technique",
			"topic_id": 210,
			"fid":57
		},
		{
			"topic_descr": "Physics",
			"topic_id": 264,
			"fid":50
		},
		{
			"topic_descr": "Physical Education and Sport",
			"topic_id": 289,
			"fid":69
		},
		{
			"topic_descr": "Chemistry",
			"topic_id": 296,
			"fid":51
		},
		{
			"topic_descr": "Economy",
			"topic_id": 305,
			"fid":67
		},
		{
			"topic_descr": "Linguistics",
			"topic_id": 314,
			"fid":"62"
		},
		{
			"topic_descr": "Linguistics",
			"topic_id": 314,
			"fid":"62"
		},
		{
			"topic_descr": "Law",
			"topic_id": 313,
			"fid":"68"
		},
		{
			"topic_descr": "empty",
			"topic_id": "",
			"fid":"31"
		},
	]
json;

return [
    'topicmapforumids' => json_decode($data),
];
