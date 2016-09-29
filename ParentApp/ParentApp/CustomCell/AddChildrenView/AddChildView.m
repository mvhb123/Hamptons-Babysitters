//
//  AddChildView.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 06/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AddChildView.h"

@implementation AddChildView


// Only override drawRect: if you perform custom drawing.
// An empty implementation adversely affects performance during animation.
- (void)drawRect:(CGRect)rect {
    // Drawing code
    //self.view_childImage.layer.cornerRadius = 50;
   // self.view_childImage.clipsToBounds = YES;
    self.lbl_childName.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    self.lbl_age.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_sex.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_ageValue.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_sexValue.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    
    [self.lbl_childName setTextColor:UIColorFromHexCode(0x04005c)];
    [self.lbl_age setTextColor:kLabelColor];
    [self.lbl_sex setTextColor:kLabelColor];
    [self.lbl_ageValue setTextColor:kLabelColor];
    [self.lbl_sexValue setTextColor:kLabelColor];
}


@end
