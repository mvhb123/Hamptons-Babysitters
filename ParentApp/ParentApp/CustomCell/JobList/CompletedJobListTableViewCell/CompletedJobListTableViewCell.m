//
//  JobListTableViewCell.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 16/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "CompletedJobListTableViewCell.h"
@implementation CompletedJobListTableViewCell

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
    self.lbl_rateValue.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_rate.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_totalChargeValue.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    self.lbl_totalCharge.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    self.lbl_jobNumber.textColor=kLabelColor;
    self.lbl_startDate.textColor=kLabelColor;
    self.lbl_endDate.textColor=kLabelColor;
    self.lbl_status.textColor=kLabelColor;
    self.lbl_viewJobNumber.textColor=kLabelColor;
    self.lbl_viewStartDate.textColor=kLabelColor;
    self.lbl_viewEndDate.textColor=kLabelColor;
    self.lbl_viewStatus.textColor=kLabelColor;
    self.lbl_babySitterName.textColor=kLabelColor;
    self.lbl_rate.textColor=kLabelColor;
    self.lbl_rateValue.textColor=kLabelColor;
    self.lbl_totalCharge.textColor=kLabelColor;
    self.lbl_totalChargeValue.textColor=kLabelColor;
    
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];
    // Configure the view for the selected state
}

@end
