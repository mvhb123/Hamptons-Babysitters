//
//  BookingCreditsTableViewCell.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 28/05/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "BookingCreditsTableViewCell.h"

@implementation BookingCreditsTableViewCell

- (void)awakeFromNib {
    // Initialization code
    self.lbl_credits.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_price.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    [self.lbl_credits setTextColor:kLabelColor];
    [self.lbl_price setTextColor:kLabelColor];
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}


@end
