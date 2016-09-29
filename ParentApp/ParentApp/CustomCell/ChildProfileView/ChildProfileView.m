//
//  ChildProfileView.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 05/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "ChildProfileView.h"

@implementation ChildProfileView


// Only override drawRect: if you perform custom drawing.
// An empty implementation adversely affects performance during animation.
- (void)drawRect:(CGRect)rect {
    
    //self.view_childImage.layer.cornerRadius = self.view_childImage.frame.size.height/2;
   // self.view_childImage.clipsToBounds = YES;
    self.lbl_childName.font = [UIFont fontWithName:RobotoRegularFont size:HeadingFieldFontSize];
    self.lbl_specialNeeds.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_alergies.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_medications.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.txtView_specialHints.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_favouriteBook.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_favouriteCartoon.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_favouriteFood.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_viewSpecialNeeds.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_viewAlergies.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_viewMedications.font =  [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_viewHelpFullHint.font =  [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_ViewFavFood.font =  [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_viewFavCartoon.font =  [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_viewFavBook.font =  [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_Sex.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_age.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_ageValue.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_sexValue.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    
    
    self.lbl_relationShipHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_parentNameHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_parentContactHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_relationShip.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_parentName.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_parentContact.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    

    
    [self.lbl_childName setTextColor:UIColorFromHexCode(0x04005c)];
    [self.lbl_specialNeeds setTextColor:kLabelColor];
    [self.lbl_alergies setTextColor:kLabelColor];
    [self.lbl_medications setTextColor:kLabelColor];
    [self.txtView_specialHints setTextColor:kLabelColor];
    [self.lbl_favouriteBook setTextColor:kLabelColor];
    [self.lbl_favouriteCartoon setTextColor:kLabelColor];
    [self.lbl_favouriteFood setTextColor:kLabelColor];
    [self.lbl_viewSpecialNeeds setTextColor:kLabelColor];
    [self.lbl_viewAlergies setTextColor:kLabelColor];
    [self.lbl_viewMedications setTextColor:kLabelColor];
    [self.lbl_viewHelpFullHint setTextColor:kLabelColor];
    [self.lbl_ViewFavFood setTextColor:kLabelColor];
    [self.lbl_viewFavCartoon setTextColor:kLabelColor];
    [self.lbl_viewFavBook setTextColor:kLabelColor];
    [self.lbl_Sex setTextColor:kLabelColor];
    [self.lbl_age setTextColor:kLabelColor];
    [self.lbl_ageValue setTextColor:kLabelColor];
    [self.lbl_sexValue setTextColor:kLabelColor];

    [self.lbl_parentContact setTextColor:kLabelColor];
    [self.lbl_parentContactHeading setTextColor:kLabelColor];
    [self.lbl_parentName setTextColor:kLabelColor];
    [self.lbl_parentNameHeading setTextColor:kLabelColor];
    [self.lbl_relationShip setTextColor:kLabelColor];
    [self.lbl_relationShipHeading setTextColor:kLabelColor];

    [self.scrollBGForChildDetail setContentSize:CGSizeMake(self.frame.size.width, 500)];
    [self.txtView_specialHints setContentOffset:CGPointMake(0, 0)];
    [self.txtView_specialHints setContentInset:UIEdgeInsetsMake(-8, 0, 0, 0)];
}


@end
