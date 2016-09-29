//
//  JobHistoryCell.m
//  SitterApp
//
//  Created by Vikram gour on 15/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "JobHistoryCell.h"

@implementation JobHistoryCell
@synthesize  lbl_jobNumber;
@synthesize lbl_jobAddress;
@synthesize lbl_startDate;
@synthesize lbl_endDate;
@synthesize lbl_ActualTime;
@synthesize lbl_rate;
@synthesize lbl_jobEarned;
@synthesize sw_paidStatus;
- (void)awakeFromNib {
    // Initialization code
     [self.lbl_jobNumber setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_jobAddress setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_startDate setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_endDate setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_ActualTime setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_rate setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_jobEarned setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_jobNumber setTextColor:kColorGrayDark];
    [self.lbl_jobAddress setTextColor:kColorGrayDark];
    [self.lbl_startDate setTextColor:kColorGrayDark];
    [self.lbl_endDate setTextColor:kColorGrayDark];
    [self.lbl_ActualTime setTextColor:kColorGrayDark];
    [self.lbl_rate setTextColor:kColorGrayDark];
    [self.lbl_jobEarned setTextColor:kColorGrayDark];
    
    [self.lbl_jobNumberHeading setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_jobAddressHeading setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_startDateHeading setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_endDateHeading setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_actualTimeHeading setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_rateHeading setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_jobEarnedHeading setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_paidHeading setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    
    [self.lbl_jobNumberHeading setTextColor:kColorGrayDark];
    [self.lbl_jobAddressHeading setTextColor:kColorGrayDark];
    [self.lbl_startDateHeading setTextColor:kColorGrayDark];
    [self.lbl_endDateHeading setTextColor:kColorGrayDark];
    [self.lbl_actualTimeHeading setTextColor:kColorGrayDark];
    [self.lbl_rateHeading setTextColor:kColorGrayDark];
    [self.lbl_jobEarnedHeading setTextColor:kColorGrayDark];
    [self.lbl_paidHeading setTextColor:kColorGrayDark];
    
    self.sw_paidStatus.tintColor=kColorGrayDark;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
