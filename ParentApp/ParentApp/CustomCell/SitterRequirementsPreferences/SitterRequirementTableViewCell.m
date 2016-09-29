//
//  SitterRequirementTableViewCell.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 27/05/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "SitterRequirementTableViewCell.h"

@implementation SitterRequirementTableViewCell

- (void)awakeFromNib {
    // Initialization code
    self.lbl_groupName.font = [UIFont fontWithName:RobotoMediumFont size:TextFieldFontSize];
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
