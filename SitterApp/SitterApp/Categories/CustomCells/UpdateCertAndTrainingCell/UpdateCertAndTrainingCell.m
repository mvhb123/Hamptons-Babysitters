//
//  UpdateCertAndTrainingCell.m
//  SitterApp
//
//  Created by Vikram gour on 08/05/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "UpdateCertAndTrainingCell.h"

@implementation UpdateCertAndTrainingCell

- (void)awakeFromNib {
    // Initialization code
    [self.lbl_certDate setFont:[UIFont fontWithName:Font_RobotoCondensed_Regular size:10.0]];
    [self.lbl_certType setFont:[UIFont fontWithName:Font_RobotoCondensed_Regular size:12.0]];
    [self.lbl_certType setNumberOfLines:2];
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
