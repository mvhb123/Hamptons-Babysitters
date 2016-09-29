//
//  SMCustomRadioButton.h
//  DynamicallyExpand
//
//  Created by Shilpa Gade on 27/04/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface SMCustomRadioButton : UIView
{
    CGSize maximumLabelSize;
    UIButton *btn_First,*btn_Second;
    UILabel *lbl_First,*lbl_Second;
    NSString *str_RadioClickedImg,*str_RadioUnClickedImg;
    NSString *str_FirstName,*str_SecondName;
     NSString *fontType;
    UIColor *fontColor;
    float fontSize;
    BOOL btnClicked,position;
    BOOL defaultsProOverwridden;
    NSArray *arr_radioBtns;
    NSMutableArray *array_radio;
    UIButton *btn;
}
-(void)setRadioBtnTextColorAndFont:(NSString *)ftype fontsize:(float)fSize forTextColor:(UIColor*) tColor;
- (void)setReqPropFroBtnState:(NSString *)firstText displaySecondText:(NSString *)secondText forCheckImageName:(NSString *)clickRadioImgName forUnchecked:(NSString *)unclickedRadioImgName buttonPosition:(BOOL)btn_Position;

- (void)setBtnState:(NSArray *)arr_btn displaySecondText:(NSString *)secondText forCheckImageName:(NSString *)clickRadioImgName forUnchecked:(NSString *)unclickedRadioImgName buttonPosition:(BOOL)btn_Position;

@end
