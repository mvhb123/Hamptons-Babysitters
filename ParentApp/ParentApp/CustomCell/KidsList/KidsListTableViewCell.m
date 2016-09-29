//
//  KidsListTableViewCell.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 10/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "KidsListTableViewCell.h"

@implementation KidsListTableViewCell

- (void)awakeFromNib {
    // Initialization code
   self.viewKidsImage.layer.cornerRadius = 50;
   self.viewKidsImage.clipsToBounds = YES;
    self.lbl_kidsName.font = [UIFont fontWithName:RobotoBoldFont size:ButtonFieldFontSize];
    self.lbl_age.font = [UIFont fontWithName:RobotoCondensedFont size:TextFieldFontSize];
    self.lbl_sex.font = [UIFont fontWithName:RobotoCondensedFont size:TextFieldFontSize];
    self.lbl_viewAge.font = [UIFont fontWithName:RobotoBoldFont size:LabelFieldFontSize];
    self.lbl_viewSex.font = [UIFont fontWithName:RobotoBoldFont size:LabelFieldFontSize];
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
