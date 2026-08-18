<?php
/**
 * 역대 성직자 / 수도자 초기 데이터.
 *
 * 구 사이트(sujica.or.kr/page/pastclergy.php)에서 옮긴 원본이다.
 * 평소 화면은 lofields(리피터) 값을 읽고, 그 값이 비어 있을 때만 이 배열을
 * 대신 쓴다. inc/admin/seed-clergy.php 가 이 배열을 리피터로 한 번 심는다.
 *
 * 날짜 표기는 원본이 제각각이라(2022.12.19. / 2018. 2.17.) 'Y. n. j.' 로
 * 통일했다. 내용은 원본 그대로다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	array(
		'slug' => 'pastors',
		'title' => '역대 주임신부',
		'kind' => 'priest',
		'rows' => array(
			array(
				'rank' => '8대',
				'name' => '황선기 마티아',
				'term' => '2026. 6. 16. ~ 현재',
				'photo' => 'vicar-8.jpg',
			),
			array(
				'rank' => '7대',
				'name' => '김태규 방그라시오',
				'term' => '2021. 6. 15. ~ 2026. 6. 15.',
				'photo' => 'vicar-7.jpg',
			),
			array(
				'rank' => '6대',
				'name' => '김희강 루도비코',
				'term' => '2016. 6. 21. ~ 2021. 6. 14.',
				'photo' => 'vicar-6.jpg',
			),
			array(
				'rank' => '5대',
				'name' => '장명원 토마스',
				'term' => '2011. 8. 30. ~ 2016. 6. 20.',
				'photo' => 'vicar-5.jpg',
			),
			array(
				'rank' => '4대',
				'name' => '조영오 도미니코 사비오',
				'term' => '2006. 9. 25. ~ 2011. 8. 29.',
				'photo' => 'vicar-4.jpg',
			),
			array(
				'rank' => '3대',
				'name' => '김영옥 가브리엘',
				'term' => '2004. 9. 30. ~ 2006. 9. 24.',
				'photo' => 'vicar-3.jpg',
			),
			array(
				'rank' => '2대',
				'name' => '박종만 요한 세례자',
				'term' => '2002. 1. 29. ~ 2004. 9. 29.',
				'photo' => 'vicar-2.jpg',
			),
			array(
				'rank' => '초대',
				'name' => '김한철 율리아노',
				'term' => '1994. 2. 3. ~ 2002. 1. 28.',
				'photo' => 'vicar-1.jpg',
			),
		),
	),
	array(
		'slug' => 'assistants',
		'title' => '역대 보좌신부',
		'kind' => 'priest',
		'rows' => array(
			array(
				'rank' => '23대',
				'name' => '최선용 바오로',
				'term' => '2025. 12. 16. ~ 현재',
				'photo' => 'assis-23.jpg',
			),
			array(
				'rank' => '22대',
				'name' => '임수빈 프란치스코',
				'term' => '2023. 12. 19. ~ 2025. 12. 15.',
				'photo' => 'assis-22.jpg',
			),
			array(
				'rank' => '21대',
				'name' => '위대혁 데메트리우스',
				'term' => '2022. 12. 20. ~ 2023. 6. 15.',
				'photo' => 'assis-21.jpg',
			),
			array(
				'rank' => '20대',
				'name' => '박승원 안젤로',
				'term' => '2020. 12. 15. ~ 2022. 12. 19.',
				'photo' => 'assis-20.jpg',
			),
			array(
				'rank' => '19대',
				'name' => '안요한 요한사도',
				'term' => '2018. 12. 18. ~ 2020. 12. 14.',
				'photo' => 'assis-19.jpg',
			),
			array(
				'rank' => '18대',
				'name' => '유정원 프란치스코',
				'term' => '2016. 12. 20. ~ 2018. 12. 17.',
				'photo' => 'assis-18.jpg',
			),
			array(
				'rank' => '17대',
				'name' => '한용희 대건안드레아',
				'term' => '2014. 12. 16. ~ 2016. 12. 20.',
				'photo' => 'assis-17.jpg',
			),
			array(
				'rank' => '16대',
				'name' => '2보좌 정석화 베드로',
				'term' => '2013. 12. 17. ~ 2014. 12. 15.',
				'photo' => 'assis-16_2.jpg',
			),
			array(
				'rank' => '16대',
				'name' => '1보좌 김만희 요셉',
				'term' => '2013. 12. 17. ~ 2014. 4. 21.',
				'photo' => 'assis-16_1.jpg',
			),
			array(
				'rank' => '15대',
				'name' => '전현수 마티아',
				'term' => '2012. 12. 18. ~ 2013. 12. 16.',
				'photo' => 'assis-15.jpg',
			),
			array(
				'rank' => '14대',
				'name' => '설동주 안드레아',
				'term' => '2011. 8. 30. ~ 2012. 8. 28.',
				'photo' => 'assis-14.jpg',
			),
			array(
				'rank' => '13대',
				'name' => '곽중헌 프란치스코',
				'term' => '2010. 8. 31. ~ 2011. 8. 29.',
				'photo' => 'assis-13.jpg',
			),
			array(
				'rank' => '12대',
				'name' => '이중교 야고보',
				'term' => '2009. 9. 1. ~ 2010. 8. 30.',
				'photo' => 'assis-12.jpg',
			),
			array(
				'rank' => '11대',
				'name' => '이정윤 요셉',
				'term' => '2008. 9. 2. ~ 2009. 8. 31.',
				'photo' => 'assis-11.jpg',
			),
			array(
				'rank' => '10대',
				'name' => '김일권 요한 사도',
				'term' => '2007. 9. 4. ~ 2008. 9. 1.',
				'photo' => 'assis-10.jpg',
			),
			array(
				'rank' => '9대',
				'name' => '표창연 프란치스코',
				'term' => '2006. 9. 26. ~ 2007. 9. 3.',
				'photo' => 'assis-09.jpg',
			),
			array(
				'rank' => '8대',
				'name' => '장대식 토마스 모어',
				'term' => '2005. 9. 23. ~ 2006. 9. 25.',
				'photo' => 'assis-08.jpg',
			),
			array(
				'rank' => '7대',
				'name' => '전삼용 요셉',
				'term' => '2004. 9. 30. ~ 2005. 9. 22.',
				'photo' => 'assis-07.jpg',
			),
			array(
				'rank' => '6대',
				'name' => '김부호 베드로',
				'term' => '2003. 1. 29. ~ 2004. 9. 29.',
				'photo' => 'assis-06.jpg',
			),
			array(
				'rank' => '5대',
				'name' => '박한현 요셉',
				'term' => '2002. 1. 29. ~ 2003. 1. 28.',
				'photo' => 'assis-05.jpg',
			),
			array(
				'rank' => '4대',
				'name' => '최규화 요한 세례자',
				'term' => '2001. 1. 30. ~ 2002. 1. 28.',
				'photo' => 'assis-04.jpg',
			),
			array(
				'rank' => '3대',
				'name' => '김태호 안토니오',
				'term' => '2000. 1. 25. ~ 2001. 1. 29.',
				'photo' => 'assis-03.jpg',
			),
			array(
				'rank' => '2대',
				'name' => '김황식 안토니오',
				'term' => '1999. 1. 25. ~ 2000. 1. 24.',
				'photo' => 'none.jpg',
			),
			array(
				'rank' => '초대',
				'name' => '조원식 요셉',
				'term' => '1998. 1. 30. ~ 1999. 1. 24.',
				'photo' => 'assis-01.jpg',
			),
		),
	),
	array(
		'slug' => 'lead_sisters',
		'title' => '역대 책임전교수녀',
		'kind' => 'sister',
		'rows' => array(
			array(
				'name' => '정은지 요한비따',
				'order' => '영원한도움의성모수도회',
				'term' => '2025. 2. 17. ~ 현재',
				'photo' => 'sis-16.jpg',
			),
			array(
				'name' => '김순희 도리스',
				'order' => '영원한도움의성모수도회',
				'term' => '2022. 2. 14. ~ 2025. 2. 16.',
				'photo' => 'sis-15.jpg',
			),
			array(
				'name' => '오영님 엘리따',
				'order' => '영원한도움의성모수도회',
				'term' => '2019. 2. 18. ~ 2022. 2. 13.',
				'photo' => 'sis-14.jpg',
			),
			array(
				'name' => '장인숙 이레네오',
				'order' => '영원한도움의성모수도회',
				'term' => '2016. 2. 16. ~ 2019. 2. 17.',
				'photo' => 'sis-13.jpg',
			),
			array(
				'name' => '임승희 미케아',
				'order' => '영원한도움의성모수도회',
				'term' => '2013. 8. 20. ~ 2016. 2. 15.',
				'photo' => 'sis-12.jpg',
			),
			array(
				'name' => '이종해 리오바',
				'order' => '영원한도움의성모수도회',
				'term' => '2010. 8. 22. ~ 2013. 8. 19.',
				'photo' => 'sis-11.jpg',
			),
			array(
				'name' => '김재숙 빅토리아',
				'order' => '영원한도움의성모수도회',
				'term' => '2009. 8. 23. ~ 2010. 8. 21.',
				'photo' => 'sis-10.jpg',
			),
			array(
				'name' => '강옥지 우술라',
				'order' => '영원한도움의성모수도회',
				'term' => '2006. 8. 22. ~ 2009. 8. 21.',
				'photo' => 'sis-09.jpg',
			),
			array(
				'name' => '송영숙 나타나엘',
				'order' => '영원한도움의성모수도회',
				'term' => '2004. 2. 25. ~ 2006. 8. 21.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '배미선 골롬바노',
				'order' => '영원한도움의성모수도회',
				'term' => '2001. 2. 20. ~ 2004. 2. 24.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '박창화 비안네',
				'order' => '영원한도움의성모수도회',
				'term' => '1999. 8. 25. ~ 2001. 2. 19.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '유경숙 타대오',
				'order' => '파티마의성모프란치스코수도회',
				'term' => '1999. 4. 13. ~ 1999. 8. 24.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '송윤근 프란치스카',
				'order' => '파티마의성모프란치스코수도회',
				'term' => '1997. 11. 30. ~ 1999. 4. 12.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '김경자 피데스',
				'order' => '파티마의성모프란치스코수도회',
				'term' => '1997. 5. 25. ~ 1999. 8. 24.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '박순화 글로리아',
				'order' => '파티마의성모프란치스코수도회',
				'term' => '1996. 1. 1. ~ 1997. 11. 29.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '김춘강 로사리아',
				'order' => '파티마의성모프란치스코수도회',
				'term' => '1994. 11. 27. ~ 1997. 5. 24.',
				'photo' => 'none.jpg',
			),
		),
	),
	array(
		'slug' => 'sisters',
		'title' => '역대 전교수녀',
		'kind' => 'sister',
		'rows' => array(
			array(
				'name' => '김정아 아론',
				'order' => '영원한도움의성모수도회',
				'term' => '2025. 8. 18. ~ 현재',
				'photo' => 'nun-20.jpg',
			),
			array(
				'name' => '강은주 엘리나',
				'order' => '영원한도움의성모수도회',
				'term' => '2024. 2. 19. ~ 2025. 8. 17.',
				'photo' => 'nun-19.jpg',
			),
			array(
				'name' => '홍하나 마리루체',
				'order' => '영원한도움의성모수도회',
				'term' => '2022. 2. 14. ~ 2024. 2. 18.',
				'photo' => 'nun-18.jpg',
			),
			array(
				'name' => '이현정 야곱',
				'order' => '영원한도움의성모수도회',
				'term' => '2020. 8. 17. ~ 2022. 2. 13.',
				'photo' => 'nun-17.jpg',
			),
			array(
				'name' => '유선미 힐데가르트',
				'order' => '영원한도움의성모수도회',
				'term' => '2019. 2. 18. ~ 2020. 8. 16.',
				'photo' => 'nun-16.jpg',
			),
			array(
				'name' => '김은혜 마리은혜',
				'order' => '영원한도움의성모수도회',
				'term' => '2018. 8. 20. ~ 2019. 2. 17.',
				'photo' => 'nun-15.jpg',
			),
			array(
				'name' => '강정숙 파우스티나',
				'order' => '영원한도움의성모수도회',
				'term' => '2018. 2. 12. ~ 2018. 8. 19.',
				'photo' => 'nun-14.jpg',
			),
			array(
				'name' => '서정민 마리아브라함',
				'order' => '영원한도움의성모수도회',
				'term' => '2016. 8. 22. ~ 2018. 2. 12.',
				'photo' => 'nun-13.jpg',
			),
			array(
				'name' => '우윤지 요안나',
				'order' => '영원한도움의성모수도회',
				'term' => '2015. 2. 9. ~ 2016. 8. 22.',
				'photo' => 'nun-12.jpg',
			),
			array(
				'name' => '최정하 요한아가다',
				'order' => '영원한도움의성모수도회',
				'term' => '2013. 8. 20. ~ 2015. 2. 9.',
				'photo' => 'nun-11.jpg',
			),
			array(
				'name' => '윤다현 카르디아',
				'order' => '영원한도움의성모수도회',
				'term' => '2012. 2. 21. ~ 2013. 8. 20.',
				'photo' => 'nun-10.jpg',
			),
			array(
				'name' => '장회경 릴리안',
				'order' => '영원한도움의성모수도회',
				'term' => '2011. 2. 21. ~ 2012. 2. 20.',
				'photo' => 'nun-09.jpg',
			),
			array(
				'name' => '김혜영 후재요한',
				'order' => '영원한도움의성모수도회',
				'term' => '2010. 2. 22. ~ 2011. 2. 21.',
				'photo' => 'nun-08.jpg',
			),
			array(
				'name' => '박계정 비오',
				'order' => '영원한도움의성모수도회',
				'term' => '2009. 2. 20. ~ 2010. 2. 21.',
				'photo' => 'nun-07.jpg',
			),
			array(
				'name' => '박희령 도나타',
				'order' => '영원한도움의성모수도회',
				'term' => '2007. 8. 21. ~ 2009. 2. 20.',
				'photo' => 'nun-06.jpg',
			),
			array(
				'name' => '최혜경 안젤리나',
				'order' => '영원한도움의성모수도회',
				'term' => '2006. 2. 18. ~ 2007. 8. 21.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '김미라 마리엔',
				'order' => '영원한도움의성모수도회',
				'term' => '2004. 8. 20. ~ 2006. 2. 18.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '김영민 브리따',
				'order' => '영원한도움의성모수도회',
				'term' => '2003. 2. 20. ~ 2004. 8. 23.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '정현경 아스테리아',
				'order' => '영원한도움의성모수도회',
				'term' => '2001. 8. 21. ~ 2003. 2. 20.',
				'photo' => 'none.jpg',
			),
			array(
				'name' => '오다운 가밀라',
				'order' => '영원한도움의성모수도회',
				'term' => '1999. 8. 25. ~ 2001. 8. 22.',
				'photo' => 'none.jpg',
			),
		),
	),
	array(
		'slug' => 'natives',
		'title' => '본당 출신 사제',
		'kind' => 'native',
		'rows' => array(
			array(
				'name' => '이종우 라파엘',
				'term' => '2019. 12. 6.',
				'photo' => 'born3.jpg',
			),
			array(
				'name' => '조원기 베드로',
				'term' => '2008. 8. 22.',
				'photo' => 'born2.jpg',
			),
			array(
				'name' => '김동진 다니엘',
				'term' => '2000. 1. 14.',
				'photo' => 'born1.jpg',
			),
		),
	),
);
