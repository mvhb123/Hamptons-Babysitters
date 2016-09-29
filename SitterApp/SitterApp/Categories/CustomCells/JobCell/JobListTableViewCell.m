//
//  JobListTableViewCell.m
//  SitterApp
//
//  Created by Shilpa Gade on 03/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "JobListTableViewCell.h"

@implementation JobListTableViewCell
@synthesize  lbl_JobNumber;
@synthesize lbl_ShowJobNumber;
@synthesize lbl_JobStartDate;
@synthesize lbl_JobShowStartDate;
@synthesize lbl_JobEndDate;
@synthesize lbl_JobShowEndDate;
@synthesize lbl_ChildrenCount;
@synthesize lbl_ShowChildrenCount;
@synthesize lbl_ChildAge;
@synthesize lbl_ShowChildAge;

- (void)awakeFromNib {
    // Initialization code
    [self.lbl_ShowChildrenCount setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_ShowJobNumber setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_JobShowStartDate setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_JobShowEndDate setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_ShowChildAge setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [self.lbl_showArea setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
   
    [self.lbl_ShowJobNumber setTextColor:kColorGrayLight];
    [self.lbl_ShowChildrenCount setTextColor:kColorGrayLight];
    [self.lbl_JobShowStartDate setTextColor:kColorGrayLight];
    [self.lbl_JobShowEndDate setTextColor:kColorGrayLight];
    [self.lbl_ShowChildAge setTextColor:kColorGrayLight];
    [self.lbl_ShowChildAge setTextColor:kColorGrayLight];
    [self.lbl_showArea setTextColor:kColorGrayLight];
    
    
    [self.lbl_JobNumber setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_JobStartDate setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_JobEndDate setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_ChildrenCount setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_ChildAge setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [self.lbl_area setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
   
    [self.lbl_JobNumber setTextColor:kColorGrayDark];
    [self.lbl_JobStartDate setTextColor:kColorGrayDark];
    [self.lbl_JobEndDate setTextColor:kColorGrayDark];
    [self.lbl_ChildrenCount setTextColor:kColorGrayDark];
    [self.lbl_ChildAge setTextColor:kColorGrayDark];
    [self.lbl_area setTextColor:kColorGrayDark];
    
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated {
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
