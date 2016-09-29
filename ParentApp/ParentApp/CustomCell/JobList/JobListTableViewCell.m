//
//  JobListTableViewCell.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 16/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "JobListTableViewCell.h"
@implementation JobListTableViewCell

- (void)awakeFromNib {
    // Initialization code
    self.lbl_jobNumber.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_startDate.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_endDate.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_status.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_viewJobNumber.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_viewStartDate.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_viewEndDate.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_viewStatus.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_babySitterName.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];

    self.lbl_jobNumber.textColor=kLabelColor;
    self.lbl_startDate.textColor=kLabelColor;
    self.lbl_endDate.textColor=kLabelColor;
    self.lbl_status.textColor=kLabelColor;
    self.lbl_viewJobNumber.textColor=kLabelColor;
    self.lbl_viewStartDate.textColor=kLabelColor;
    self.lbl_viewEndDate.textColor=kLabelColor;
    self.lbl_viewStatus.textColor=kLabelColor;
    self.lbl_babySitterName.textColor=kLabelColor;
    
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
