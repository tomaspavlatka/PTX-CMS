<?php
final class PTX_Google {
	
	/**
	 * ziska sourdnice stredu kraje
	 * @param $idDistrict - id kraje
	 * @return pole s lat a lng
	 */
	public static function getDistrictCenter($idDistrict) {
		$int = (int)$idDistrict;
		switch($int) {
			case 1:
				    return array('lng'=>50.07124366044474, 'lat'=>14.43603515625,'zoom' => 10);
				    break;
            case 2:
                    return array('lng'=>50.02185841773444, 'lat'=>14.644775390625,'zoom' => 8);
                    break;
            case 3:
                    return array('lng'=>49.18170338770662, 'lat'=>14.39208984375,'zoom' => 8);
                    break;
            case 4:
                    return array('lng'=>49.603590524348704, 'lat'=>13.29345703125,'zoom' => 8);
                    break;
            case 5:
                    return array('lng'=>50.14874640066278, 'lat'=>12.76611328125,'zoom' => 8);
                    break;
            case 6:
                    return array('lng'=>50.51342652633956, 'lat'=>13.82080078125,'zoom' => 8);
                    break;
            case 7:
                    return array('lng'=>50.72602429659469, 'lat'=>15.1226806640625,'zoom' => 8);
                    break;
            case 8:
                    return array('lng'=>50.39101007095669, 'lat'=>15.8917236328125,'zoom' => 8);
                    break;
            case 9:
                    return array('lng'=>49.89109559573688, 'lat'=>16.2432861328125,'zoom' => 9);
                    break;
            case 10:
                    return array('lng'=>49.3787965643504, 'lat'=>15.732421875,'zoom' => 8);
                    break;
            case 11:
                    return array('lng'=>49.01625665778159, 'lat'=>16.7376708984375,'zoom' => 8);
                    break;
            case 12:
                    return array('lng'=>49.80608653412716, 'lat'=>16.995849609375,'zoom' => 8);
                    break;
            case 13:
                    return array('lng'=>49.837982453084834, 'lat'=>17.7978515625,'zoom' => 8);
                    break;
            case 14:
                    return array('lng'=>49.167338606291075, 'lat'=>17.7264404296875,'zoom' => 8);
                    break;
		}
	}
}